<?php

namespace App\Filament\Resources\Payments\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListPaymentActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Payments\PaymentResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - Paiement #:id', ['id' => $this->record->id]);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités - Paiement #:id', ['id' => $this->record->id]);
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur ce paiement');
    }
}

