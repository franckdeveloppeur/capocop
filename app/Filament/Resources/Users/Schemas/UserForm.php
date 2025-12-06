<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: INFORMATIONS DE COMPTE ===
                Section::make('Informations de Compte')
                    ->description('Détails d\'identification de l\'utilisateur')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom Complet')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Jean Dupont')
                            ->hint('Le nom de l\'utilisateur'),

                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('exemple@mail.com')
                            ->hint('Email unique pour la connexion'),

                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->placeholder('+33 6 12 34 56 78')
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->hint('Numéro de téléphone de contact'),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Vérifié le')
                            ->hint('Date de vérification de l\'email'),
                    ]),

                // === SECTION 2: SÉCURITÉ ===
                Section::make('Sécurité')
                    ->description('Paramètres de sécurité et authentification')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible()
                    ->schema([
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('••••••••')
                            ->hint('Minimum 8 caractères'),

                        Toggle::make('two_factor_enabled')
                            ->label('Authentification à Deux Facteurs')
                            ->hint('Activez pour sécuriser le compte'),

                        Textarea::make('two_factor_secret')
                            ->label('Clé Secrète 2FA')
                            ->placeholder('Clé secrète...')
                            ->columnSpanFull()
                            ->hint('Code secret pour la 2FA'),

                        Textarea::make('two_factor_recovery_codes')
                            ->label('Codes de Récupération')
                            ->placeholder('Codes séparés par des lignes...')
                            ->columnSpanFull()
                            ->hint('Codes de secours pour la 2FA'),

                        DateTimePicker::make('two_factor_confirmed_at')
                            ->label('2FA Confirmée le')
                            ->hint('Date de confirmation de la 2FA'),
                    ]),

                // === SECTION 3: RÔLE ET STATUT ===
                Section::make('Rôle et Statut')
                    ->description('Permissions et activation du compte')
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->schema([
                        Select::make('role')
                            ->label('Rôle')
                            ->options([
                                'customer' => 'Client',
                                'vendor' => 'Vendeur',
                                'admin' => 'Administrateur',
                            ])
                            ->required()
                            ->default('customer')
                            ->placeholder('Sélectionnez un rôle')
                            ->hint('Définit les permissions de l\'utilisateur'),

                        Toggle::make('is_active')
                            ->label('Compte Actif')
                            ->inline(false)
                            ->hint('Désactivez pour bloquer l\'accès'),

                        TextInput::make('current_team_id')
                            ->label('Équipe Actuelle (ID)')
                            ->numeric()
                            ->hint('ID de l\'équipe actuelle'),
                    ]),

                // === SECTION 4: PROFIL ===
                // Section::make('Profil')
                //     ->description('Informations supplémentaires du profil')
                //     ->icon('heroicon-o-photo')
                //     ->collapsible()
                //     ->schema([
                //         TextInput::make('profile_photo_path')
                //             ->label('Chemin Photo de Profil')
                //             ->placeholder('/storage/profiles/photo.jpg')
                //             ->hint('Chemin vers l\'image du profil'),
                //     ]),

                // === SECTION 5: ADRESSES (OPTIONAL) ===
                Section::make('Adresses de Livraison')
                    ->description('Gérer les adresses de livraison de l\'utilisateur')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // Placeholder pour relation d'adresses
                        // La relation 'addresses' devra être gérée via RelationManager séparé
                        Textarea::make('addresses_info')
                            ->label('Information sur les Adresses')
                            ->disabled()
                            ->placeholder('Les adresses associées à cet utilisateur s\'affichent dans l\'onglet Adresses ci-dessous.')
                            ->columnSpanFull()
                            ->hint('Utiliser le RelationManager pour ajouter/modifier les adresses'),
                    ]),
            ]);
    }
}
