<?php

namespace App\Filament\MyAccount\Widgets;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MySpendingChart extends ChartWidget
{
    protected ?string $heading = 'Mes Dépenses Mensuelles (XAF)';
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $user = Auth::user();
        
        // Récupérer les dépenses mensuelles des 12 derniers mois pour l'utilisateur
        $data = Trend::model(Order::class)
            ->between(
                start: now()->subMonths(11)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->sum('total_amount');
        
        // Filtrer les données pour ne garder que les commandes de l'utilisateur
        $userOrders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($orders) {
                return $orders->sum('total_amount');
            });
        
        // Mapper les données pour correspondre aux mois
        $monthlyData = [];
        foreach ($data as $value) {
            $monthKey = date('Y-m', strtotime($value->date));
            $monthlyData[] = $userOrders->get($monthKey, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dépenses (XAF)',
                    'data' => $monthlyData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => date('M Y', strtotime($value->date)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

