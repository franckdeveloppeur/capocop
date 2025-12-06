<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === INFORMATIONS GÉNÉRALES ===
                TextInput::make('label')
                    ->label('Label de l\'Adresse')
                    ->placeholder('Ex: Domicile, Bureau, Autre...')
                    ->hint('Identifiez cette adresse'),

                TextInput::make('full_name')
                    ->label('Nom Complet')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: Jean Dupont'),

                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->required()
                    ->placeholder('+33 6 12 34 56 78')
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                // === LOCALISATION (FUSED GROUP) ===
                FusedGroup::make([
                    Select::make('country')
                        ->label('Pays')
                        ->placeholder('Sélectionnez un pays')
                        ->options([
                            'FR' => 'France',
                            'BE' => 'Belgique',
                            'CH' => 'Suisse',
                            'CA' => 'Canada',
                            'US' => 'États-Unis',
                            'GB' => 'Royaume-Uni',
                            'DE' => 'Allemagne',
                            'IT' => 'Italie',
                            'ES' => 'Espagne',
                        ])
                        ->required()
                        ->columnSpan(2),

                    TextInput::make('city')
                        ->label('Ville')
                        ->placeholder('Ex: Paris')
                        ->required()
                        ->columnSpan(1),
                ])
                    ->label('Localisation')
                    ->columns(3),

                // === CODE POSTAL ===
                TextInput::make('postal_code')
                    ->label('Code Postal')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('75001'),

                // === ADRESSE COMPLÈTE ===
                TextInput::make('line1')
                    ->label('Adresse Ligne 1')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: 123 rue de la Paix')
                    ->columnSpanFull(),

                TextInput::make('line2')
                    ->label('Adresse Ligne 2 (Appartement, Bâtiment...)')
                    ->maxLength(255)
                    ->placeholder('Ex: Appartement 5B')
                    ->columnSpanFull(),

                // === COORDONNÉES GPS (OPTIONNEL) ===
                FusedGroup::make([
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric()
                        ->placeholder('48.8566')
                        ->columnSpan(1),

                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric()
                        ->placeholder('2.3522')
                        ->columnSpan(1),
                ])
                    ->label('Coordonnées GPS (Optionnel)')
                    ->columns(2),
            ]);
    }
}
