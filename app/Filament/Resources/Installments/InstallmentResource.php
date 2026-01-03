<?php

namespace App\Filament\Resources\Installments;

use App\Filament\Resources\Installments\Pages\CreateInstallment;
use App\Filament\Resources\Installments\Pages\EditInstallment;
use App\Filament\Resources\Installments\Pages\ListInstallments;
use App\Filament\Resources\Installments\Pages\ViewInstallment;
use App\Filament\Resources\Installments\Schemas\InstallmentForm;
use App\Filament\Resources\Installments\Schemas\InstallmentInfolist;
use App\Filament\Resources\Installments\Tables\InstallmentsTable;
use App\Models\Installment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InstallmentResource extends Resource
{
    protected static ?string $model = Installment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return __('Échéance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Échéances');
    }

    public static function form(Schema $schema): Schema
    {
        return InstallmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InstallmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstallmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Les relations seront gérées via les champs dans l'infolist
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstallments::route('/'),
            'create' => CreateInstallment::route('/create'),
            'view' => ViewInstallment::route('/{record}'),
            'edit' => EditInstallment::route('/{record}/edit'),
        ];
    }
}
