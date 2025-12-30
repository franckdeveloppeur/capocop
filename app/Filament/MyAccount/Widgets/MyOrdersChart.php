<?php

namespace App\Filament\MyAccount\Widgets;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\Auth;

class MyOrdersChart extends ApexChartWidget
{
    protected static ?string $chartId = 'myOrdersChart';

    protected static ?string $heading = 'Mes Commandes';

    protected function getOptions(): array
    {
        $user = Auth::user();
        
        // Récupérer les tendances des commandes des 12 derniers mois pour l'utilisateur
        $data = Trend::model(Order::class)
            ->between(
                start: now()->subMonths(11)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();
        
        // Filtrer les données pour ne garder que les commandes de l'utilisateur
        $userOrders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($orders) {
                return $orders->count();
            });
        
        // Mapper les données pour correspondre aux mois
        $monthlyData = [];
        foreach ($data as $value) {
            $monthKey = date('Y-m', strtotime($value->date));
            $monthlyData[] = $userOrders->get($monthKey, 0);
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Mes Commandes',
                    'data' => $monthlyData,
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => date('M Y', strtotime($value->date)))->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.3,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
        ];
    }
}

