<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Image principale
                ImageEntry::make('main_image')
                    ->label('Image Principale')
                    ->getStateUsing(function ($record) {
                        try {
                            // Load media relationship if not loaded
                            if (!$record->relationLoaded('media')) {
                                $record->load('media');
                            }
                            
                            $firstMedia = $record->media->first();
                            if ($firstMedia) {
                                $path = $firstMedia->custom_properties['full_path'] ?? ('products/' . $firstMedia->file_name);
                                // Return full URL for better compatibility
                                return asset('storage/' . $path);
                            }
                        } catch (\Throwable $e) {
                            // Log error for debugging
                            \Log::error('Error loading product image: ' . $e->getMessage());
                        }
                        return null;
                    })
                    ->height(250)
                    ->width(250)
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->extraImgAttributes([
                        'class' => 'object-cover rounded-lg',
                        'loading' => 'lazy',
                    ])
                    ->columnSpanFull(),

                // Informations générales
                TextEntry::make('id')
                    ->label('ID')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                TextEntry::make('shop.name')
                    ->label('Boutique')
                    ->icon('heroicon-o-building-storefront')
                    ->placeholder('Aucune boutique'),

                TextEntry::make('status')
                    ->label('Statut')
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

                TextEntry::make('title')
                    ->label('Titre du Produit')
                    ->weight('bold')
                    ->size('lg')
                    ->columnSpanFull(),

                TextEntry::make('slug')
                    ->label('Slug (URL)')
                    ->icon('heroicon-o-link')
                    ->copyable()
                    ->columnSpanFull(),

                TextEntry::make('description')
                    ->label('Description')
                    ->html()
                    ->placeholder('Aucune description')
                    ->columnSpanFull(),

                // Tarification
                TextEntry::make('base_price')
                    ->label('Prix de Base')
                    ->money('XAF')
                    ->size('lg')
                    ->weight('bold')
                    ->color('success'),

                TextEntry::make('price_promo')
                    ->label('Prix Promotionnel')
                    ->money('XAF')
                    ->placeholder('Aucune promotion')
                    ->color('warning'),

                IconEntry::make('stock_manage')
                    ->label('Gestion du Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Dates
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y à H:i')
                    ->icon('heroicon-o-calendar'),

                TextEntry::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y à H:i')
                    ->icon('heroicon-o-calendar')
                    ->placeholder('Jamais modifié'),
            ]);
    }
}
