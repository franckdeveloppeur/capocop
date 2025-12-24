<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger les relations nécessaires pour éviter les N+1 queries
        $this->record->load([
            'installmentPlan.installments',
            'payments',
            'items',
            'user',
            'shop',
            'address',
        ]);

        return $data;
    }
}
