<?php

namespace App\Filament\Resources\Installments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\InstallmentPlan;
use App\Models\Payment;

class InstallmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->description('Détails de l\'échéance')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('plan_id')
                            ->label(__('Plan de Paiement'))
                            ->relationship('plan', 'id', fn ($query) => $query->with('order'))
                            ->getOptionLabelFromRecordUsing(fn (InstallmentPlan $record): string => 
                                __('Commande') . ' #' . $record->order_id . ' - ' . number_format($record->total_amount, 0, ',', ' ') . ' XAF'
                            )
                            ->searchable(['order_id'])
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated()
                            ->helperText(__('Le plan de paiement ne peut pas être modifié après création')),

                        DatePicker::make('due_date')
                            ->label(__('Date d\'Échéance'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText(__('Date à laquelle cette échéance doit être payée')),

                        TextInput::make('amount')
                            ->label(__('Montant'))
                            ->required()
                            ->numeric()
                            ->prefix('XAF')
                            ->suffixIcon('heroicon-o-currency-dollar')
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText(__('Montant à payer pour cette échéance')),

                        Select::make('status')
                            ->label(__('Statut'))
                            ->options([
                                'pending' => __('En attente'),
                                'paid' => __('Payée'),
                                'overdue' => __('En retard'),
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false)
                            ->helperText(__('Statut actuel de l\'échéance')),
                    ]),

                Section::make('Informations de Paiement')
                    ->description('Détails du paiement associé')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('paid_at')
                            ->label(__('Date de Paiement'))
                            ->native(false)
                            ->displayFormat('d/m/Y à H:i')
                            ->helperText(__('Date et heure à laquelle l\'échéance a été payée'))
                            ->visible(fn ($get) => $get('status') === 'paid'),

                        Select::make('payment_id')
                            ->label(__('Paiement Associé'))
                            ->relationship('payment', 'transaction_ref', fn ($query) => $query->with('order'))
                            ->getOptionLabelFromRecordUsing(fn (Payment $record): string => 
                                __('Transaction') . ': ' . ($record->transaction_ref ?? $record->id) . ' - ' . number_format($record->amount, 0, ',', ' ') . ' XAF'
                            )
                            ->searchable(['transaction_ref', 'id'])
                            ->helperText(__('Paiement lié à cette échéance (optionnel)'))
                            ->placeholder(__('Aucun paiement associé')),
                    ]),
            ]);
    }
}
