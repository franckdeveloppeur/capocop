<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            \Filament\Actions\Action::make('activities')
                ->label(__('Activités'))
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn () => ProductResource::getUrl('activities', ['record' => $this->record])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure media relation is loaded
        $this->record->load('media', 'shop', 'categories', 'tags');
        
        return $data;
    }
}
