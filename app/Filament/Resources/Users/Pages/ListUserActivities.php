<?php

namespace App\Filament\Resources\Users\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListUserActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Users\UserResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités de :name', ['name' => $this->record->name ?? $this->record->email]);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités de :name', ['name' => $this->record->name ?? $this->record->email]);
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur cet utilisateur');
    }
}

