<?php

namespace App\Filament\Resources\Products\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListProductActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Products\ProductResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - :title', ['title' => $this->record->title ?? 'Produit']);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités - :title', ['title' => $this->record->title ?? 'Produit']);
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur ce produit');
    }
}

