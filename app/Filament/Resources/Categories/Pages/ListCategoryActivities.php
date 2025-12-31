<?php

namespace App\Filament\Resources\Categories\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Filament\Actions\Action;

class ListCategoryActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Categories\CategoryResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - :name', ['name' => $this->record->name ?? 'Catégorie']);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités');
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur :name', [
            'name' => $this->record->name ?? 'cette catégorie'
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Retour'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
        ];
    }
}

