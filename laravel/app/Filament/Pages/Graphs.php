<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class Graphs extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;
    
    protected string $view = 'filament.pages.graphs';

    public string $selectedMonth;
    public array $monthOptions = [];
    public ?string $selectedCategoryForSpending = null;

    public function mount(): void
    {
        $expenseMonths = Expense::query()
            ->selectRaw('DATE_FORMAT(execution_date, "%Y-%m") as month')
            ->groupBy('month')
            ->pluck('month')
            ->all();

        $incomeMonths = Income::query()
            ->selectRaw('DATE_FORMAT(execution_date, "%Y-%m") as month')
            ->groupBy('month')
            ->pluck('month')
            ->all();

        $months = collect($expenseMonths)
            ->merge($incomeMonths)
            ->unique()
            ->sort()
            ->values();

        $this->monthOptions = $months
            ->mapWithKeys(fn ($m) => [$m => Carbon::createFromFormat('Y-m', $m)->format('F Y')])
            ->toArray();

        $this->selectedMonth = Carbon::now()->format('Y-m');
    }

    public function updatedSelectedMonth(): void
    {
        $this->dispatch('updateFilter', filter: $this->selectedMonth);
    }
}
