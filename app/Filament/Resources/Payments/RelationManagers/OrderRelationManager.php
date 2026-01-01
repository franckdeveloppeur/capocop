<?php

namespace App\Filament\Resources\Payments\RelationManagers;

use App\Models\Order;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'order';

    protected static ?string $title = 'Commande Associée';

    protected static ?string $recordTitleAttribute = 'id';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();
        
        // Pour une relation belongsTo, parent::getTableQuery() peut retourner null
        // Dans ce cas, on construit la requête manuellement
        if ($query === null) {
            $orderId = $this->getOwnerRecord()->order_id;
            
            if ($orderId) {
                return Order::query()
                    ->where('id', $orderId)
                    ->with(['user', 'address', 'shop']);
            }
            
            // Retourner une requête vide si pas de commande associée
            return Order::query()->whereRaw('1 = 0');
        }
        
        return $query->with(['user', 'address', 'shop']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->limit(8)
                    ->copyable(),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email Client')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('shipping_amount')
                    ->label('Frais de Livraison')
                    ->money('XAF', locale: 'fr')
                    ->sortable(),

                TextColumn::make('discount_amount')
                    ->label('Remise')
                    ->money('XAF', locale: 'fr')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->badge()
                    ->placeholder('N/A'),

                IconColumn::make('is_installment')
                    ->label('Paiement Échelonné')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

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
                ViewAction::make()
                    ->url(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucune commande associée')
            ->emptyStateDescription('Cette commande n\'est pas disponible.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}

