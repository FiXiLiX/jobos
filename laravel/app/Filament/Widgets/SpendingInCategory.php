<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Livewire\Attributes\On;

class SpendingInCategory extends ApexChartWidget
{
    protected static ?string $chartId = 'spendingInCategory';

    protected static ?string $heading = 'Spending by Subcategory';

    public ?string $monthFilter = null;

    #[On('updateFilter')]
    public function updateFilter($filter): void
    {
        $this->monthFilter = $filter;
    }

    public function mount(array $parameters = []): void
    {
        parent::mount($parameters);
        
        // Store the month filter from page parameters
        if (isset($parameters['filter'])) {
            $this->monthFilter = $parameters['filter'];
        }
    }

    protected function getOptions(): array
    {
        // Use the month filter from page
        $month = $this->monthFilter ?? Carbon::now()->format('Y-m');
        
        // Validate month format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = Carbon::now()->format('Y-m');
        }
        
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $query = Expense::query()
            ->whereBetween('execution_date', [$start->toDateString(), $end->toDateString()])
            ->with('budgetSubcategory');

        $spendingBySubcategory = $query
            ->get()
            ->groupBy(fn(Expense $expense) => $expense->budgetSubcategory?->name ?? 'Uncategorized')
            ->map(fn($group) => round($group->sum('amount'), 2))
            ->sort()
            ->all();

        $labels = array_keys($spendingBySubcategory);
        $data = array_values($spendingBySubcategory);

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

    public function getColumnSpan(): int|string|array
    {
        return 1;
    }
}
