<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('product_id')
                    ->relationship('product', 'title')
                    ->required(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('title'),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
