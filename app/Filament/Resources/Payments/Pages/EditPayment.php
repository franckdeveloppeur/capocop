<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
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
        $this->record->load('order');
        
        // Convert meta array to JSON string for textarea
        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta'] = json_encode($data['meta'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert meta JSON string back to array
        if (isset($data['meta']) && is_string($data['meta'])) {
            $decoded = json_decode($data['meta'], true);
            $data['meta'] = $decoded !== null ? $decoded : [];
        }
        
        return $data;
    }
}

