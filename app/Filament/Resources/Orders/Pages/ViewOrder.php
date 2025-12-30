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
            \Filament\Actions\Action::make('activities')
                ->label(__('Activités'))
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn () => OrderResource::getUrl('activities', ['record' => $this->record])),
        ];
    }
}
