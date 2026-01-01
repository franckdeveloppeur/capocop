<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->limit(8),
                TextColumn::make('order.id')
                    ->label('Commande')
                    ->searchable()
                    ->sortable()
                    ->limit(8)
                    ->formatStateUsing(fn ($state, $record) => $state ? substr($state, 0, 8) : 'N/A')
                    ->placeholder('N/A'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('method')
                    ->label('Méthode')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mobile_money' => 'Mobile Money',
                        'card' => 'Carte bancaire',
                        'wallet' => 'Capocop Pay',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'wallet' => 'success',
                        'card' => 'info',
                        'mobile_money' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Statut')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'success' => 'Réussi',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('transaction_ref')
                    ->label('Référence')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activities')
                    ->label(__('Activités'))
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->url(fn ($record) => \App\Filament\Resources\Payments\PaymentResource::getUrl('activities', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

