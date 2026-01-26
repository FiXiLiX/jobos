<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SpendingByCategory extends ApexChartWidget
{
    protected static ?string $chartId = 'spendingByCategory';

    protected static ?string $heading = 'Spending by Category';

    protected function getOptions(): array
    {
        $month = $this->filter ?? Carbon::now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $spendingByCategory = Expense::query()
            ->whereBetween('execution_date', [$start->toDateString(), $end->toDateString()])
            ->with('budgetSubcategory.category')
            ->get()
            ->groupBy(fn(Expense $expense) => $expense->budgetSubcategory?->category->name ?? 'Uncategorized')
            ->map(fn($group) => round($group->sum('amount'), 2))
            ->all();

        $labels = array_keys($spendingByCategory);
        $data = array_values($spendingByCategory);

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 320,
            ],
            'labels' => $labels,
            'series' => $data,
            'colors' => ['#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'],
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
