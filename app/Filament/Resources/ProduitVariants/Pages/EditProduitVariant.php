<?php

namespace App\Filament\Resources\ProduitVariants\Pages;

use App\Filament\Resources\ProduitVariants\ProduitVariantResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProduitVariant extends EditRecord
{
    protected static string $resource = ProduitVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
