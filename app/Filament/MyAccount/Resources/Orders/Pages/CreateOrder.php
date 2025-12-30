<?php

namespace App\Filament\MyAccount\Resources\Orders\Pages;

use App\Filament\MyAccount\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Assigner automatiquement l'utilisateur connecté
        $data['user_id'] = Auth::id();
        
        return $data;
    }
}

