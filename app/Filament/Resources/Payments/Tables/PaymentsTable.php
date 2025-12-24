<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->sortable()
                    ->copyable()
                    ->limit(8)
                    ->weight('bold'),

                TextColumn::make('order.id')
                    ->label('Commande')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '#' . substr($state, 0, 8))
                    ->icon('heroicon-o-shopping-bag')
                    ->color('primary'),

                TextColumn::make('order.user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non spécifié')
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->alignEnd(),

                TextColumn::make('method')
                    ->label('Méthode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'mobile_money' => 'Mobile Money',
                        'card' => 'Carte',
                        'wallet' => 'Portefeuille',
                        default => 'Non spécifié',
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'success' => 'Réussi',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                        default => $state,
                    }),

                TextColumn::make('transaction_ref')
                    ->label('Référence')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(20)
                    ->icon('heroicon-o-document-duplicate')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'success' => 'Réussi',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                    ])
                    ->multiple()
                    ->preload(),

                SelectFilter::make('method')
                    ->label('Méthode de paiement')
                    ->options([
                        'mobile_money' => 'Mobile Money',
                        'card' => 'Carte bancaire',
                        'wallet' => 'Portefeuille',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Voir'),
                EditAction::make()
                    ->label('Modifier'),
                DeleteAction::make()
                    ->label('Supprimer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Supprimer'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}

