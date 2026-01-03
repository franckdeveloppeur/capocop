<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Installment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    protected static ?string $title = 'Échéances';

    protected static ?string $recordTitleAttribute = 'id';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();
        
        // Si la relation n'existe pas, retourner une requête vide
        if ($query === null) {
            return Installment::query()->whereRaw('1 = 0');
        }
        
        // Charger les relations nécessaires
        return $query->with(['payment', 'plan'])
            ->orderBy('due_date', 'asc');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('due_date')
                    ->label('Date d\'Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => match ($record->status) {
                        'overdue' => 'danger',
                        'paid' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->dateTime('d/m/Y à H:i')
                    ->placeholder('Non payé')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('payment.transaction_ref')
                    ->label('Référence Paiement')
                    ->placeholder('N/A')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('mark_as_paid')
                    ->label('Marquer comme Payée')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid' && Auth::user()->role === 'admin')
                    ->requiresConfirmation()
                    ->modalHeading('Marquer l\'échéance comme payée')
                    ->modalDescription('Cette action marquera l\'échéance comme payée et enregistrera la date de paiement à maintenant.')
                    ->form([
                        DatePicker::make('paid_at')
                            ->label('Date de Paiement')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => $data['paid_at'],
                        ]);

                        Notification::make()
                            ->title('Échéance marquée comme payée')
                            ->success()
                            ->send();
                    }),

                Action::make('update_status')
                    ->label('Modifier le Statut')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn () => Auth::user()->role === 'admin')
                    ->form([
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'paid' => 'Payée',
                                'overdue' => 'En retard',
                            ])
                            ->required()
                            ->default(fn ($record) => $record->status),

                        DatePicker::make('paid_at')
                            ->label('Date de Paiement')
                            ->visible(fn ($get) => $get('status') === 'paid')
                            ->default(fn ($record) => $record->paid_at ?? now()),
                    ])
                    ->action(function ($record, array $data) {
                        $updateData = [
                            'status' => $data['status'],
                        ];

                        if ($data['status'] === 'paid' && isset($data['paid_at'])) {
                            $updateData['paid_at'] = $data['paid_at'];
                        } elseif ($data['status'] !== 'paid') {
                            $updateData['paid_at'] = null;
                        }

                        $record->update($updateData);

                        Notification::make()
                            ->title('Statut de l\'échéance mis à jour')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('due_date', 'asc')
            ->emptyStateHeading('Aucune échéance')
            ->emptyStateDescription('Cette commande n\'a pas de plan de paiement échelonné.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}

