<?php

namespace App\Filament\Resources\Coupons\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use Filament\Actions\Action;

class ListCouponActivities extends ListActivities
{
    protected static string $resource = \App\Filament\Resources\Coupons\CouponResource::class;

    public function getTitle(): string
    {
        return __('Journal d\'activités - Coupon :code', ['code' => $this->record->code ?? 'N/A']);
    }

    public function getHeading(): string
    {
        return __('Journal d\'activités');
    }

    public function getSubheading(): ?string
    {
        return __('Historique complet des modifications et actions effectuées sur le coupon :code', [
            'code' => $this->record->code ?? 'N/A'
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

