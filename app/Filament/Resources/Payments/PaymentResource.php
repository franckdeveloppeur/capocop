<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\ManagePayments;
use App\Filament\Resources\Payments\Pages\ListPaymentActivities;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Schemas\PaymentInfolist;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getModelLabel(): string
    {
        return __('Paiement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Paiements');
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['order']); // Charger la relation order pour éviter les requêtes N+1
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
            'activities' => ListPaymentActivities::route('/{record}/activities'),
        ];
    }
}
