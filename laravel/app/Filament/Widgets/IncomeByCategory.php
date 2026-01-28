<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class IncomeByCategory extends ApexChartWidget
{
    protected static ?string $chartId = 'incomeByCategory';

    protected static ?string $heading = 'Income by Category';

    protected function getOptions(): array
    {
        $month = $this->filter ?? Carbon::now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $incomeByCategory = Income::query()
            ->whereBetween('execution_date', [$start->toDateString(), $end->toDateString()])
            ->with('budgetIncome')
            ->get()
            ->groupBy(fn(Income $income) => $income->budgetIncome?->name ?? 'Uncategorized')
            ->map(fn($group) => round($group->sum('amount_normalized'), 2))
            ->all();

        $labels = array_keys($incomeByCategory);
        $data = array_values($incomeByCategory);
        // dd($labels, $data);
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 320,
            ],
            'labels' => $labels,
            'series' => $data,
            'colors' => ['#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#eab308', '#22c55e'],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '65%',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
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
        return 1;
    }
}
