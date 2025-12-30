<?php

namespace App\Filament\Resources\Orders\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListOrderActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Orders\OrderResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - Commande #:id', ['id' => $this->record->id]);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités - Commande #:id', ['id' => $this->record->id]);
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur cette commande');
    }
}

