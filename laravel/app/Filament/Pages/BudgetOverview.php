<?php

namespace App\Filament\Pages;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\BudgetSubcategory;
use App\Models\BudgetSubcategoryBudgeted;
use App\Models\BudgetCategoryBudgeted;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BudgetOverview extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    
    protected string $view = 'filament.pages.budget-overview';

    public ?Budget $budget = null;

    public Collection $categories;

    public Collection $incomes;

    public string $currentMonth;

    public string $subcategoryName = '';

    public array $editingBudgeted = [];

    public bool $showOptions = false;

    public function mount(): void
    {
        $this->currentMonth = Carbon::now()->startOfMonth()->toDateString();
        $this->loadBudget();
        $this->loadCategories();
        $this->loadIncomes();
    }

    public function previousMonth(): void
    {
        $this->shiftMonth(-1);
    }

    public function nextMonth(): void
    {
        $this->shiftMonth(1);
    }

    public function copyPreviousMonthBudget(): void
    {
        $date = Carbon::parse($this->currentMonth);
        $previousDate = $date->copy()->subMonthNoOverflow();

        // Ensure current budget exists
        $this->budget = Budget::firstOrCreate([
            'year' => $date->year,
            'month' => $date->month,
        ]);

        // Find previous month budget
        $previousBudget = Budget::where('year', $previousDate->year)
            ->where('month', $previousDate->month)
            ->first();

        if (!$previousBudget) {
            $this->showOptions = false;
            return;
        }

        // Copy expense budgeted amounts
        $previousSubBudgets = BudgetSubcategoryBudgeted::where('budget_id', $previousBudget->id)->get();
        foreach ($previousSubBudgets as $entry) {
            BudgetSubcategoryBudgeted::updateOrCreate(
                [
                    'budget_id' => $this->budget->id,
                    'subcategory_id' => $entry->subcategory_id,
                ],
                [
                    'budgeted' => $entry->budgeted,
                ]
            );
        }

        // Copy income expected amounts
        $previousIncomeBudgets = BudgetCategoryBudgeted::where('budget_id', $previousBudget->id)->get();
        foreach ($previousIncomeBudgets as $entry) {
            BudgetCategoryBudgeted::updateOrCreate(
                [
                    'budget_id' => $this->budget->id,
                    'budget_income_id' => $entry->budget_income_id,
                ],
                [
                    'expected' => $entry->expected,
                    'created_by' => $entry->created_by,
                ]
            );
        }

        $this->showOptions = false;
        $this->loadCategories();
        $this->loadIncomes();
    }

    public function resetBudget(): void
    {
        if (!$this->budget) {
            $this->showOptions = false;
            return;
        }

        BudgetSubcategoryBudgeted::where('budget_id', $this->budget->id)->update(['budgeted' => 0]);
        BudgetCategoryBudgeted::where('budget_id', $this->budget->id)->update(['expected' => 0]);

        $this->showOptions = false;
        $this->loadCategories();
        $this->loadIncomes();
    }

    public function updateBudgeted(int $subcategoryId, float $value): void
    {
        if (!$this->budget) {
            return;
        }

        BudgetSubcategoryBudgeted::updateOrCreate(
            [
                'budget_id' => $this->budget->id,
                'subcategory_id' => $subcategoryId,
            ],
            [
                'budgeted' => $value,
            ]
        );

        $this->loadCategories();
        $this->loadIncomes();
    }

    public function updateExpected(int $incomeId, float $value): void
    {
        if (!$this->budget) {
            return;
        }

        \App\Models\BudgetCategoryBudgeted::updateOrCreate(
            [
                'budget_id' => $this->budget->id,
                'budget_income_id' => $incomeId,
            ],
            [
                'expected' => $value,
                'created_by' => Auth::id(),
            ]
        );

        $this->loadIncomes();
    }

    public function createSubcategory(int $categoryId): void
    {
        BudgetSubcategory::create([
            'name' => $this->subcategoryName,
            'category_id' => $categoryId,
        ]);

        $this->subcategoryName = '';
        $this->loadCategories();
        
        $this->dispatch('close-modal', id: 'create-subcategory-' . $categoryId);
    }

    public function deleteSubcategory(int $subcategoryId): void
    {
        $subcategory = BudgetSubcategory::find($subcategoryId);
        
        if ($subcategory) {
            $subcategory->delete();
            $this->loadCategories();
        }
        
        $this->dispatch('close-modal', id: 'delete-subcategory-' . $subcategoryId);
    }

    public function deleteIncome(int $incomeId): void
    {
        $income = \App\Models\BudgetIncome::find($incomeId);
        
        if ($income) {
            $income->delete();
            $this->loadIncomes();
        }
        
        $this->dispatch('close-modal', id: 'delete-income-' . $incomeId);
    }

    public function renameCategory(int $categoryId, string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }

        $category = BudgetCategory::find($categoryId);
        if ($category) {
            $category->update(['name' => $name]);
            $this->loadCategories();
        }

        $this->dispatch('close-modal', id: 'edit-category-' . $categoryId);
    }

    public function renameSubcategory(int $subcategoryId, string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }

        $subcategory = BudgetSubcategory::find($subcategoryId);
        if ($subcategory) {
            $subcategory->update(['name' => $name]);
            $this->loadCategories();
        }

        $this->dispatch('close-modal', id: 'edit-subcategory-' . $subcategoryId);
    }

    public function renameIncome(int $incomeId, string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }

        $income = \App\Models\BudgetIncome::find($incomeId);
        if ($income) {
            $income->update(['name' => $name]);
            $this->loadIncomes();
        }

        $this->dispatch('close-modal', id: 'edit-income-' . $incomeId);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createCategory')
                ->label('+ Add Spending Category')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    BudgetCategory::create([
                        'name' => $data['name'],
                        'created_by' => Auth::id(),
                    ]);
                    $this->loadCategories();
                }),
            Action::make('createIncomeCategory')
                ->label('+ Add Income Category')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    \App\Models\BudgetIncome::create([
                        'name' => $data['name'],
                        'created_by' => Auth::id(),
                    ]);
                    $this->loadIncomes();
                }),
        ];
    }

    private function shiftMonth(int $delta): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)
            ->addMonthsNoOverflow($delta)
            ->startOfMonth()
            ->toDateString();

        $this->showOptions = false;

        $this->loadBudget();
        $this->loadCategories();
        $this->loadIncomes();
    }

    private function loadBudget(): void
    {
        $date = Carbon::parse($this->currentMonth);

        $this->budget = Budget::where('year', $date->year)
            ->where('month', $date->month)
            ->first();
    }

    private function loadCategories(): void
    {
        $date = Carbon::parse($this->currentMonth);
        
        $this->categories = BudgetCategory::with([
            'subcategories.budgetedAmounts' => function ($query) {
                if ($this->budget) {
                    $query->where('budget_id', $this->budget->id);
                }
            },
            'subcategories.expenses' => function ($query) use ($date) {
                $query->whereYear('execution_date', $date->year)
                      ->whereMonth('execution_date', $date->month);
            }
        ])->get();
    }

    private function loadIncomes(): void
    {
        $date = Carbon::parse($this->currentMonth);
        
        $this->incomes = \App\Models\BudgetIncome::with([
            'budgetedAmounts' => function ($query) {
                if ($this->budget) {
                    $query->where('budget_id', $this->budget->id);
                }
            },
            'incomes' => function ($query) use ($date) {
                $query->whereYear('execution_date', $date->year)
                      ->whereMonth('execution_date', $date->month);
            }
        ])->get();
    }
}
