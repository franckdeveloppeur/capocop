<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $recordTitleAttribute = 'file_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_name')
                    ->label('Fichier')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->imageEditor()
                    ->directory('products')
                    ->maxSize(512)
                    ->reorderable()
                    ->appendFiles()
                    ->hint('Téléchargez les images du produit. Max 512k0 par image.')
                    ->columnSpanFull()
                    ->required(),

                // Optionnel: collection_name hidden with default
                // Filament will fill the file_name attribute and create the related Media record
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_name')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => 
                        // Prefer the stored full_path in custom_properties, fallback to products/<file_name>
                        data_get($record, 'custom_properties.full_path') ?? ('products/' . data_get($record, 'file_name'))
                    )
                    ->disk('public'),

                TextColumn::make('file_name')
                    ->label('Nom du fichier'),

                TextColumn::make('size')
                    ->label('Taille')
                    ->formatStateUsing(fn($state) => $state ? number_format($state / 1024, 2) . ' KB' : '-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Create action will be provided by Filament automatically
            ])
            ->actions([
                // Filament default view/edit/delete are fine
            ])
            ->bulkActions([
                //
            ]);
    }
}
