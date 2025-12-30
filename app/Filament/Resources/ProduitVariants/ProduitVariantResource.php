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

    public static function getModelLabel(): string
    {
        return __('Variante de produit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Variantes de produit');
    }

    protected static ?string $recordTitleAttribute = 'sku';

    public static function getGloballySearchableAttributes(): array
    {
        return ['sku', 'product.title'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Produit' => $record->product->title ?? 'N/A',
            'Prix' => number_format($record->price, 0, ',', ' ') . ' XOF',
            'Stock' => $record->stock,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['product']);
    }

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
