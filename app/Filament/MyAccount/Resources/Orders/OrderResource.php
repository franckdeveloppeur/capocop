<?php

namespace App\Filament\MyAccount\Resources\Orders;

use App\Filament\MyAccount\Resources\Orders\Pages\CreateOrder;
use App\Filament\MyAccount\Resources\Orders\Pages\EditOrder;
use App\Filament\MyAccount\Resources\Orders\Pages\ListOrders;
use App\Filament\MyAccount\Resources\Orders\Pages\ViewOrder;
use App\Filament\MyAccount\Resources\Orders\Schemas\OrderForm;
use App\Filament\MyAccount\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\MyAccount\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public static function getModelLabel(): string
    {
        return __('Commande');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Mes Commandes');
    }

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $globalSearchSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'status', 'payment_method', 'address.city'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Statut' => $record->status,
            'Montant' => number_format($record->total_amount, 0, ',', ' ') . ' XOF',
            'Date' => $record->created_at->format('d/m/Y'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['address']);
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return 'Commande #' . strtoupper(substr($record->id, 0, 8));
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
