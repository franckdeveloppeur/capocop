<?php

namespace App\Filament\Resources\Payments\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Filament\Actions\Action;

class ListPaymentActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Payments\PaymentResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - Paiement #:id', ['id' => $this->record->id]);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités');
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur le paiement #:id', [
            'id' => $this->record->id
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

