<?php

namespace App\Filament\MyAccount\Widgets;

use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class MyLatestOrdersOverview extends TableWidget
{
    protected static ?string $heading = 'Mes Dernières Commandes';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        
        return $table
            ->query(
                Order::query()
                    ->where('user_id', $user->id)
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->limit(8),
                    
                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('XAF')
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'paid' => 'success',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'processing' => 'En traitement',
                        'paid' => 'Payée',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->sortable(),
                    
                TextColumn::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

