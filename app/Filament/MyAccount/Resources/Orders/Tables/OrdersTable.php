<?php

namespace App\Filament\MyAccount\Resources\Orders\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        $user = Auth::user();
        
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('user_id', $user->id)->with(['items.product.media']))
            ->columns([
                ImageColumn::make('items.product.media')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        try {
                            $firstItem = $record->items()->with('product.media')->first();
                            if ($firstItem && $firstItem->product) {
                                $firstMedia = $firstItem->product->media()->first();
                                if ($firstMedia) {
                                    $path = $firstMedia->custom_properties['full_path'] ?? ('products/' . $firstMedia->file_name);
                                    return asset('storage/' . $path);
                                }
                            }
                        } catch (\Exception $e) {
                            // Ignorer les erreurs
                        }
                        return null;
                    })
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->square()
                    ->size(60)
                    ->toggleable(),
                    
                TextColumn::make('id')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable()
                    ->limit(8)
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('XAF')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
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
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'processing' => 'En traitement',
                        'paid' => 'Payée',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        'refunded' => 'Remboursée',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-arrow-path',
                        'paid' => 'heroicon-o-banknotes',
                        'shipped' => 'heroicon-o-truck',
                        'delivered' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        'refunded' => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),
                    
                TextColumn::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'Espèces',
                        'card' => 'Carte bancaire',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Virement bancaire',
                        default => $state ?? 'Non spécifiée',
                    })
                    ->placeholder('Non spécifiée'),
                    
                IconColumn::make('is_installment')
                    ->label('Échelonné')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'processing' => 'En traitement',
                        'paid' => 'Payée',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        'refunded' => 'Remboursée',
                    ])
                    ->native(false),
                    
                SelectFilter::make('payment_method')
                    ->label('Méthode de Paiement')
                    ->options([
                        'cash' => 'Espèces',
                        'card' => 'Carte bancaire',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Virement bancaire',
                    ])
                    ->native(false),
                    
                SelectFilter::make('is_installment')
                    ->label('Paiement Échelonné')
                    ->options([
                        true => 'Oui',
                        false => 'Non',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucune commande')
            ->emptyStateDescription('Vous n\'avez pas encore passé de commande.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}

