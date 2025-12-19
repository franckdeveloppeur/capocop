<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Utilisateurs', User::count())
                ->description('Utilisateurs inscrits')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 10, 15, 20, 25, 30, User::count()]),
                
            Stat::make('Total Commandes', Order::count())
                ->description('Commandes totales')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning')
                ->chart([5, 10, 15, 20, 25, Order::count()]),
                
            Stat::make('Total Produits', Product::count())
                ->description('Produits disponibles')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart([10, 20, 30, 40, 50, Product::count()]),
                
            Stat::make('Revenus Total', 'XAF ' . number_format(Order::sum('total_amount'), 0, ',', ' '))
                ->description('Montant total des commandes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Commandes en Attente', Order::where('status', 'pending')->count())
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Commandes Livrées', Order::where('status', 'delivered')->count())
                ->description('Terminées avec succès')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
