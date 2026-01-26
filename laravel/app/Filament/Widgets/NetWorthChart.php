<?php

namespace App\Filament\Widgets;

use App\Support\Finance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NetWorthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'netWorthChart';

    protected static ?string $heading = 'Net Worth';

    protected function getOptions(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        [$labels, $data] = Finance::netWorthSeries($start, $end);
        // dd($labels, $data);
        return [
            'chart' => [
                'type' => 'line',
                'height' => 320,
                'background' => 'transparent',
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'rotate' => -45,
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
            'series' => [
                [
                    'name' => 'Net Worth',
                    'data' => $data,
                ],
            ],
            'colors' => ['#1eff00ff'],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 5,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.9,
                    'opacityTo' => 0.4,
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => null,
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
