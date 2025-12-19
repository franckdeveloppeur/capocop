<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('media')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        try {
                            $first = data_get($record, 'media.0');
                            if ($first) {
                                $path = data_get($first, 'custom_properties.full_path') ?? ('products/' . data_get($first, 'file_name'));
                                    return asset('storage/' . $path);
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }

                        return null;
                    })
                    ->disk('public')
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->square()
                    ->size(60),

                TextColumn::make('shop.name')
                    ->label('Boutique')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-storefront')
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                TextColumn::make('base_price')
                    ->label('Prix de Base')
                    ->money('XAF')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('price_promo')
                    ->label('Prix Promo')
                    ->money('XAF')
                    ->sortable()
                    ->placeholder('—')
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Publié',
                        'draft' => 'Brouillon',
                        'archived' => 'Archivé',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'draft' => 'heroicon-o-clock',
                        'archived' => 'heroicon-o-archive-box',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                IconColumn::make('stock_manage')
                    ->label('Gestion Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

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
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Publié',
                        'draft' => 'Brouillon',
                        'archived' => 'Archivé',
                    ])
                    ->default('active'),

                SelectFilter::make('shop_id')
                    ->label('Boutique')
                    ->relationship('shop', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('stock_manage')
                    ->label('Gestion du Stock')
                    ->options([
                        '1' => 'Activée',
                        '0' => 'Désactivée',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('archive')
                    ->label('Archiver')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Archiver ce produit')
                    ->modalDescription('Êtes-vous sûr de vouloir archiver ce produit ? Il ne sera plus visible pour les clients.')
                    ->modalSubmitActionLabel('Oui, archiver')
                    ->action(function ($record) {
                        $record->update(['status' => 'archived']);
                        
                        Notification::make()
                            ->title('Produit archivé')
                            ->success()
                            ->body('Le produit a été archivé avec succès.')
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'archived'),
                    
                Action::make('unarchive')
                    ->label('Désarchiver')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Désarchiver ce produit')
                    ->modalDescription('Voulez-vous rendre ce produit visible à nouveau ?')
                    ->modalSubmitActionLabel('Oui, désarchiver')
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        
                        Notification::make()
                            ->title('Produit désarchivé')
                            ->success()
                            ->body('Le produit est maintenant actif.')
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'archived'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
