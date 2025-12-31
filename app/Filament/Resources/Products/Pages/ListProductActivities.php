<?php

namespace App\Filament\Resources\Products\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Filament\Actions\Action;

class ListProductActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Products\ProductResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - :title', ['title' => $this->record->title ?? 'Produit']);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités');
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur :title', [
            'title' => $this->record->title ?? 'ce produit'
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Retour'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('view', ['record' => $this->record])),
        ];
    }
}

