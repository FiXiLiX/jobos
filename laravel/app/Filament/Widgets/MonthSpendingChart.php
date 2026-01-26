<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MonthSpendingChart extends ApexChartWidget
{
    public ?string $selectedMonth = null;
    /**
    * Chart Id
    *
    * @var string
    */
    protected static ?string $chartId = 'monthSpendingChart';

    /**
    * Widget Title
    *
    * @var string|null
    */
    protected static ?string $heading = 'Monthly Spending';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $month = $this->filter ?? Carbon::now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $days = Collection::times($start->daysInMonth, function (int $day) use ($start) {
            return $start->copy()->day($day);
        });

        $expensesByDay = Expense::query()
            ->whereBetween('execution_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy(fn (Expense $expense) => Carbon::parse($expense->execution_date)->day)
            ->map(fn ($group) => $group->sum('amount'));

        $labels = $days->map(fn (Carbon $date) => $date->format('d M'))->all();

        $runningTotal = 0.0;
        $data = $days
            ->map(function (Carbon $date) use (&$runningTotal, $expensesByDay) {
                $runningTotal += (float) ($expensesByDay[$date->day] ?? 0);
                return (float) round($runningTotal, 0);
            })
            ->all();

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
                    'name' => 'Spent',
                    'data' => $data,
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'columnWidth' => '55%',
                    'borderRadius' => 4,
                    'horizontal' => false,
                ],
            ],
            'fill' => [
                'opacity' => 0.9,
            ],
            'colors' => ['#f59e0b'],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => null,
                ],
            ],
        ];
    }

    // Shared month filter is provided externally via Livewire parameter `filter`.

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
