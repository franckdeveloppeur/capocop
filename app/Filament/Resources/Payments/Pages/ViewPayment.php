<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            \Filament\Actions\Action::make('activities')
                ->label(__('Activités'))
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn () => PaymentResource::getUrl('activities', ['record' => $this->record])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure order relation is loaded
        $this->record->load('order.user', 'order.shop');
        
        return $data;
    }
}

