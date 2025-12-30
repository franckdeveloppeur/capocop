<?php

namespace App\Filament\MyAccount\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyOrdersStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->sum('total_amount');
        $pendingOrders = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $deliveredOrders = Order::where('user_id', $user->id)->where('status', 'delivered')->count();
        $processingOrders = Order::where('user_id', $user->id)->where('status', 'processing')->count();
        $paidOrders = Order::where('user_id', $user->id)->where('status', 'paid')->count();

        // Calculer les commandes des 6 derniers mois pour le graphique
        $recentOrders = [];
        for ($i = 5; $i >= 0; $i--) {
            $recentOrders[] = Order::where('user_id', $user->id)
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->whereYear('created_at', now()->subMonths($i)->year)
                ->count();
        }
        $recentOrders[] = $totalOrders;

        return [
            Stat::make('Mes Commandes', $totalOrders)
                ->description('Total de mes commandes')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($recentOrders),
                
            Stat::make('Total Dépensé', 'XAF ' . number_format($totalSpent, 0, ',', ' '))
                ->description('Montant total de mes commandes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Commandes en Attente', $pendingOrders)
                ->description('En attente de traitement')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Commandes Livrées', $deliveredOrders)
                ->description('Terminées avec succès')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('En Traitement', $processingOrders)
                ->description('En cours de préparation')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
                
            Stat::make('Payées', $paidOrders)
                ->description('Commandes payées')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}

