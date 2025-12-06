<?php

namespace App\Filament\Resources\ProduitVariants\Pages;

use App\Filament\Resources\ProduitVariants\ProduitVariantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProduitVariants extends ListRecords
{
    protected static string $resource = ProduitVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
