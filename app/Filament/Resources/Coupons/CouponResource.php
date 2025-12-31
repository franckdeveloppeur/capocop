<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Filament\Resources\Coupons\Pages\ListCouponActivities;
use App\Filament\Resources\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Coupons\Schemas\CouponInfolist;
use App\Filament\Resources\Coupons\Tables\CouponsTable;
use App\Models\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function getModelLabel(): string
    {
        return __('Coupon');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Coupons');
    }

    protected static ?string $recordTitleAttribute = 'code';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'type'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        $now = now();
        $isValid = $record->valid_from <= $now && $record->valid_until >= $now;
        $isAvailable = $record->usage_limit === null || $record->used_count < $record->usage_limit;
        
        return [
            'Type' => $record->type,
            'Valeur' => number_format($record->value, 0, ',', ' ') . ($record->type === 'percentage' ? '%' : ' XOF'),
            'Statut' => ($isValid && $isAvailable) ? 'Disponible' : 'Non disponible',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CouponForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CouponInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
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
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'view' => ViewCoupon::route('/{record}'),
            'edit' => EditCoupon::route('/{record}/edit'),
            'activities' => ListCouponActivities::route('/{record}/activities'),
        ];
    }
}
