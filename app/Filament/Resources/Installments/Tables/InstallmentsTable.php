<?php

namespace App\Filament\Resources\Installments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstallmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-hashtag')
                    ->limit(8),

                TextColumn::make('plan.order_id')
                    ->label(__('Commande'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn ($record) => $record->plan?->order 
                        ? \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record->plan->order])
                        : null)
                    ->openUrlInNewTab(),

                TextColumn::make('due_date')
                    ->label(__('Date d\'Échéance'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => match ($record->status) {
                        'overdue' => 'danger',
                        'paid' => 'success',
                        default => 'warning',
                    })
                    ->icon('heroicon-o-calendar')
                    ->weight('bold'),

                TextColumn::make('amount')
                    ->label(__('Montant'))
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar'),

                TextColumn::make('status')
                    ->label(__('Statut'))
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'paid' => 'heroicon-o-check-circle',
                        'overdue' => 'heroicon-o-exclamation-triangle',
                        'pending' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => __('Payée'),
                        'overdue' => __('En retard'),
                        'pending' => __('En attente'),
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label(__('Date de Paiement'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-check-circle')
                    ->badge()
                    ->color('success')
                    ->placeholder(__('Non payée'))
                    ->toggleable(),

                TextColumn::make('payment.transaction_ref')
                    ->label(__('Transaction'))
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-credit-card')
                    ->placeholder(__('Non associée'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Créé le'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Modifié le'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Statut'))
                    ->options([
                        'pending' => __('En attente'),
                        'paid' => __('Payée'),
                        'overdue' => __('En retard'),
                    ])
                    ->multiple(),

                SelectFilter::make('plan.status')
                    ->label(__('Statut du Plan'))
                    ->relationship('plan', 'status')
                    ->options([
                        'active' => __('Actif'),
                        'completed' => __('Terminé'),
                        'defaulted' => __('En défaut'),
                        'cancelled' => __('Annulé'),
                    ])
                    ->multiple(),
            ])
            ->defaultSort('due_date', 'asc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('Aucune échéance'))
            ->emptyStateDescription(__('Aucune échéance n\'a été trouvée.'))
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
