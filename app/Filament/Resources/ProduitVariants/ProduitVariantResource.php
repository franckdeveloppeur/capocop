<?php

namespace App\Filament\Resources\ProduitVariants;

use App\Filament\Resources\ProduitVariants\Pages\CreateProduitVariant;
use App\Filament\Resources\ProduitVariants\Pages\EditProduitVariant;
use App\Filament\Resources\ProduitVariants\Pages\ListProduitVariants;
use App\Filament\Resources\ProduitVariants\Pages\ViewProduitVariant;
use App\Filament\Resources\ProduitVariants\Schemas\ProduitVariantForm;
use App\Filament\Resources\ProduitVariants\Schemas\ProduitVariantInfolist;
use App\Filament\Resources\ProduitVariants\Tables\ProduitVariantsTable;
use App\Models\ProductVariant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduitVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    public static function form(Schema $schema): Schema
    {
        return ProduitVariantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProduitVariantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduitVariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduitVariants::route('/'),
            'create' => CreateProduitVariant::route('/create'),
            'view' => ViewProduitVariant::route('/{record}'),
            'edit' => EditProduitVariant::route('/{record}/edit'),
        ];
    }
}
