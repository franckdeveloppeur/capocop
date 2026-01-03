<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID avec badge et copie
                TextColumn::make('id')
                    ->label(__('ID Commande'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('ID copié !'))
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-hashtag')
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state): string => '#' . strtoupper(substr($state, 0, 8)))
                    ->description(fn ($record) => __('Créée le') . ' ' . $record->created_at->format('d/m/Y'))
                    ->sortable(),

                // Client avec avatar et email
                TextColumn::make('user.name')
                    ->label(__('Client'))
                    ->searchable(['user.name', 'user.email'])
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->color('primary')
                    ->weight('bold')
                    ->description(fn ($record) => $record->user->email ?? '')
                    ->url(fn ($record) => $record->user 
                        ? \App\Filament\Resources\Users\UserResource::getUrl('view', ['record' => $record->user])
                        : null)
                    ->openUrlInNewTab(),

                // Boutique
                TextColumn::make('shop.name')
                    ->label(__('Boutique'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-storefront')
                    ->color('info')
                    ->badge()
                    ->toggleable(),

                // Montant total avec formatage riche
                TextColumn::make('total_amount')
                    ->label(__('Montant Total'))
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->weight('bold')
                    ->size('lg')
                    ->description(fn ($record) => 
                        $record->discount_amount > 0 
                            ? __('Réduction') . ': ' . number_format($record->discount_amount, 0, ',', ' ') . ' XAF'
                            : null
                    )
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label(__('Total Général'))
                            ->money('XAF', locale: 'fr'),
                    ]),

                // Statut avec badges colorés et icônes
                TextColumn::make('status')
                    ->label(__('Statut'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-arrow-path',
                        'shipped' => 'heroicon-o-truck',
                        'delivered' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        'refunded' => 'heroicon-o-arrow-uturn-left',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('En attente'),
                        'processing' => __('En traitement'),
                        'shipped' => __('Expédiée'),
                        'delivered' => __('Livrée'),
                        'cancelled' => __('Annulée'),
                        'refunded' => __('Remboursée'),
                        default => $state,
                    }),

                // Méthode de paiement
                TextColumn::make('payment_method')
                    ->label(__('Méthode de Paiement'))
                    ->searchable()
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {
                        'mobile_money' => 'heroicon-o-device-phone-mobile',
                        'card' => 'heroicon-o-credit-card',
                        'wallet' => 'heroicon-o-wallet',
                        'cash' => 'heroicon-o-banknotes',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'mobile_money' => __('Mobile Money'),
                        'card' => __('Carte bancaire'),
                        'wallet' => __('CAPOCOP'),
                        'cash' => __('Espèces'),
                        default => __('Non spécifié'),
                    })
                    ->toggleable(),

                // Paiement échelonné
                IconColumn::make('is_installment')
                    ->label(__('Paiement Échelonné'))
                    ->boolean()
                    ->trueIcon('heroicon-o-calendar-days')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                // Nombre d'articles
                TextColumn::make('items_count')
                    ->label(__('Articles'))
                    ->counts('items')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-bag')
                    ->sortable()
                    ->toggleable(),

                // Adresse de livraison
                TextColumn::make('address')
                    ->label(__('Adresse'))
                    ->formatStateUsing(function ($record) {
                        if (!$record->address) {
                            return __('Non spécifiée');
                        }
                        $address = $record->address;
                        $parts = array_filter([
                            $address->line1 ?? null,
                            $address->line2 ?? null,
                            $address->city ?? null,
                            $address->postal_code ?? null,
                            $address->country ?? null,
                        ]);
                        return implode(', ', $parts) ?: __('Non spécifiée');
                    })
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Date de création
                TextColumn::make('created_at')
                    ->label(__('Date de Commande'))
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->toggleable(),

                // Date de mise à jour
                TextColumn::make('updated_at')
                    ->label(__('Modifiée le'))
                    ->dateTime('d/m/Y à H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtre par statut
                SelectFilter::make('status')
                    ->label(__('Statut'))
                    ->options([
                        'pending' => __('En attente'),
                        'processing' => __('En traitement'),
                        'shipped' => __('Expédiée'),
                        'delivered' => __('Livrée'),
                        'cancelled' => __('Annulée'),
                        'refunded' => __('Remboursée'),
                    ])
                    ->multiple()
                    ->native(false)
                    ->preload(),

                // Filtre par méthode de paiement
                SelectFilter::make('payment_method')
                    ->label(__('Méthode de Paiement'))
                    ->options([
                        'mobile_money' => __('Mobile Money'),
                        'card' => __('Carte bancaire'),
                        'wallet' => __('CAPOCOP'),
                        'cash' => __('Espèces'),
                    ])
                    ->multiple()
                    ->native(false)
                    ->preload(),

                // Filtre paiement échelonné
                TernaryFilter::make('is_installment')
                    ->label(__('Paiement Échelonné'))
                    ->placeholder(__('Toutes les commandes'))
                    ->trueLabel(__('Avec paiement échelonné'))
                    ->falseLabel(__('Sans paiement échelonné'))
                    ->native(false),

                // Filtre par boutique
                SelectFilter::make('shop_id')
                    ->label(__('Boutique'))
                    ->relationship('shop', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->native(false),

                // Filtre par client
                SelectFilter::make('user_id')
                    ->label(__('Client'))
                    ->relationship('user', 'name', fn (Builder $query) => $query->orderBy('name'))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->native(false),

                // Filtre par date de création
                Filter::make('created_at')
                    ->label(__('Date de Commande'))
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('Du')),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('Au')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // Filtre par montant
                Filter::make('total_amount')
                    ->label(__('Montant Total'))
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->label(__('Montant minimum'))
                            ->numeric()
                            ->prefix('XAF'),
                        \Filament\Forms\Components\TextInput::make('amount_until')
                            ->label(__('Montant maximum'))
                            ->numeric()
                            ->prefix('XAF'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_until'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->groups([
                Group::make('status')
                    ->label(__('Par Statut'))
                    ->collapsible(),
                Group::make('payment_method')
                    ->label(__('Par Méthode de Paiement'))
                    ->collapsible(),
                Group::make('shop.name')
                    ->label(__('Par Boutique'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('Par Date'))
                    ->date()
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label(__('Voir'))
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                EditAction::make()
                    ->label(__('Modifier'))
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                Action::make('activities')
                    ->label(__('Activités'))
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->url(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('activities', ['record' => $record])),
                Action::make('duplicate')
                    ->label(__('Dupliquer'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('Dupliquer la commande'))
                    ->modalDescription(__('Êtes-vous sûr de vouloir dupliquer cette commande ?'))
                    ->action(function ($record) {
                        // Logique de duplication à implémenter
                        \Filament\Notifications\Notification::make()
                            ->title(__('Commande dupliquée'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => auth()->user()?->role === 'admin'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Supprimer'))
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),
                    Action::make('export')
                        ->label(__('Exporter'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Logique d'export à implémenter
                            \Filament\Notifications\Notification::make()
                                ->title(__('Export en cours'))
                                ->body(__('Les commandes seront exportées dans quelques instants.'))
                                ->info()
                                ->send();
                        }),
                    Action::make('mark_as_processing')
                        ->label(__('Marquer en traitement'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['status' => 'processing']);
                            \Filament\Notifications\Notification::make()
                                ->title(__('Commandes mises à jour'))
                                ->body(__(':count commande(s) marquée(s) comme en traitement.', ['count' => $records->count()]))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading(__('Aucune commande'))
            ->emptyStateDescription(__('Commencez par créer une nouvelle commande.'))
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateActions([
                Action::make('create')
                    ->label(__('Créer une commande'))
                    ->url(\App\Filament\Resources\Orders\OrderResource::getUrl('create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ])
            ->poll('30s')
            ->deferLoading()
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
