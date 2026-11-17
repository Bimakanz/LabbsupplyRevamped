<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Produk Terlaris';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $rows = OrderItem::query()
            ->select('name', DB::raw('SUM(quantity) as total'))
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all(),
                    'backgroundColor' => ['#4981d4', '#546e8e', '#8aa6c6', '#b9c9dc', '#dbe4ee'],
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
