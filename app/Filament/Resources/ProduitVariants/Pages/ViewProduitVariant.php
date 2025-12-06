<?php

namespace App\Filament\Resources\ProduitVariants\Pages;

use App\Filament\Resources\ProduitVariants\ProduitVariantResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduitVariant extends ViewRecord
{
    protected static string $resource = ProduitVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
