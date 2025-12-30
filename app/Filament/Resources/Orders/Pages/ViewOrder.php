<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadInvoice')
                ->label('Télécharger la facture')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('orders.invoice', $this->record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
