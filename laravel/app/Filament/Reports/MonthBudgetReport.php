<?php

namespace App\Filament\Reports;

use EightyNine\Reports\Components\Body\Table;
use EightyNine\Reports\Components\Body\TextColumn;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Report;
use App\Settings\GeneralSettings;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form as SchemaForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\BudgetSubcategoryBudgeted;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\Account;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;

class MonthBudgetReport extends Report
{
    public ?string $heading = "Monthly Budget Report";

    // Route params will be injected by Filament/Livewire
    public ?int $month = null;
    public ?int $year = null;

    public function header(Schema $schema): Schema
    {
        $monthLabel = $this->getSelectedMonth()->format('F Y');

        return $schema
            ->components([
                Text::make('Monthly Budget Report')->title(),
                Text::make('Month: ' . $monthLabel)->subTitle(),
            ]);
    }

    // Override navigation URL to always point to current month
    public static function getNavigationUrl(): string
    {
        $now = now();
        return url('/admin/reports/month-budget-report/' . $now->year . '/' . $now->month);
    }


    public function body(Schema $schema): Schema
    {
        $selectedMonth = $this->getSelectedMonth();

        return $schema
            ->components([
                Text::make('Account State')->fontBold(),
                Table::make()
                    ->columns([
                        TextColumn::make('account')->label('Account'),
                        TextColumn::make('beginning')->label('Beginning of Month'),
                        TextColumn::make('ending')->label('End of Month'),
                    ])
                    ->data(fn () => $this->getAccountStateRows($selectedMonth)),

                Text::make('Budget (per subcategory)')->fontBold(),
                Table::make()
                    ->columns([
                        TextColumn::make('category')->label('Category'),
                        TextColumn::make('subcategory')->label('Subcategory'),
                        TextColumn::make('budgeted')->label('Budgeted')->numeric(2),
                        TextColumn::make('spent')->label('Spent')->numeric(2),
                    ])
                    ->data(fn () => $this->getBudgetRows($selectedMonth)),

                Text::make('Incomes')->fontBold(),
                Table::make()
                    ->columns([
                        TextColumn::make('date')->label('Date'),
                        TextColumn::make('source')->label('Income Source'),
                        TextColumn::make('amount')->label('Amount'),
                        TextColumn::make('normalized')->label('Normalized'),
                    ])
                    ->data(fn () => $this->getIncomeRows($selectedMonth)),

                Text::make('Expenses')->fontBold(),
                Table::make()
                    ->columns([
                        TextColumn::make('date')->label('Date'),
                        TextColumn::make('category')->label('Category'),
                        TextColumn::make('amount')->label('Amount'),
                        TextColumn::make('normalized')->label('Normalized'),
                    ])
                    ->data(fn () => $this->getExpenseRows($selectedMonth)),

                Text::make('Transfers')->fontBold(),
                Table::make()
                    ->columns([
                        TextColumn::make('date')->label('Date'),
                        TextColumn::make('from')->label('From Account'),
                        TextColumn::make('to')->label('To Account'),
                        TextColumn::make('taken')->label('Amount Taken'),
                        TextColumn::make('received')->label('Amount Received'),
                    ])
                    ->data(fn () => $this->getTransferRows($selectedMonth)),
            ]);
    }

    public function footer(Schema $schema): Schema
    {
        return $schema
            ->components([
                Text::make('Generated on '.now()->format('Y-m-d H:i')),
            ]);
    }


    // Remove filter form and filter logic

    protected function getSelectedMonth(): Carbon
    {
        $month = $this->month ?? now()->month;
        $year = $this->year ?? now()->year;
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = now()->month;
        }
        if (!is_numeric($year) || $year < 1900) {
            $year = now()->year;
        }
        return Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
    }

    // Add custom route registration for /admin/reports/month-budget-report/{year}/{month}
    public static function routes($panel): void
    {
        \Illuminate\Support\Facades\Route::get('/month-budget-report/{year?}/{month?}', static::class)
            ->name('month-budget-report');
    }

    protected function getAccountStateRows(Carbon $month)
    {
        $base = strtoupper(app(GeneralSettings::class)->default_currency);
        $monthStart = $month->toDateString();
        $monthEnd = $month->copy()->endOfMonth()->toDateString();
        $today = now()->toDateString();
        $endDate = min($monthEnd, $today); // Use current date if month hasn't ended

        return Account::all()
            ->map(function ($account) use ($monthStart, $monthEnd, $endDate, $base) {
                // Get account state at beginning of month
                $beginningBalance = $this->getAccountBalanceAtDate($account->id, $monthStart);

                // Get account state at end of month (or today if month hasn't ended)
                $endingBalance = $this->getAccountBalanceAtDate($account->id, $endDate);

                $currencyCode = $account->amountCurrency?->code ?? $base;

                return [
                    'account' => $account->name,
                    'beginning' => number_format((float) $beginningBalance, 2) . ' ' . $currencyCode,
                    'ending' => number_format((float) $endingBalance, 2) . ' ' . $currencyCode,
                ];
            });
    }

    protected function getAccountBalanceAtDate($accountId, $date)
    {
        $account = Account::find($accountId);
        if (!$account) {
            return 0;
        }

        // Start with the initial account amount
        $balance = $account->amount;

        // Get all income transactions for this account up to and including the date
        $incomeTotal = Income::where('account_id', $accountId)
            ->where('execution_date', '<=', $date)
            ->sum('amount');

        // Get all expense transactions up to and including the date
        $expenseTotal = Expense::where('execution_date', '<=', $date)
            ->where('account_id', $accountId)
            ->sum('amount');

        // Get all outgoing transfers up to and including the date
        $outgoingTransfers = Transfer::where('from_account_id', $accountId)
            ->where('created_at', '<=', $date . ' 23:59:59')
            ->sum('amount_taken');

        // Get all incoming transfers up to and including the date
        $incomingTransfers = Transfer::where('to_account_id', $accountId)
            ->where('created_at', '<=', $date . ' 23:59:59')
            ->sum('amount_received');

        // Balance = initial amount + income - expenses + transfers in - transfers out
        $balance = $account->amount + $incomeTotal - $expenseTotal + $incomingTransfers - $outgoingTransfers;

        return $balance;
    }

    protected function getBudgetRows(Carbon $month)
    {
        $base = strtoupper(app(GeneralSettings::class)->default_currency);

        return BudgetSubcategoryBudgeted::query()
            ->with(['subcategory.category', 'budget'])
            ->whereHas('budget', fn ($q) => $q->where('year', $month->year)->where('month', $month->month))
            ->get()
            ->map(fn ($item) => [
                'category' => $item->subcategory?->category?->name ?? 'Uncategorized',
                'subcategory' => $item->subcategory?->name ?? '—',
                'budgeted' => number_format((float) $item->budgeted, 2).' '.$base,
                'spent' => number_format((float) $this->getSubcategorySpent($item->subcategory_id, $month), 2).' '.$base,
            ]);
    }

    protected function getSubcategorySpent($subcategoryId, Carbon $month)
    {
        return Expense::query()
            ->where('budget_subcategory_id', $subcategoryId)
            ->whereBetween('execution_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->sum('amount');
    }

    protected function getIncomeRows(Carbon $month)
    {
        $base = strtoupper(app(GeneralSettings::class)->default_currency);

        return Income::query()
            ->with(['budgetIncome', 'amountCurrency'])
            ->whereBetween('execution_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->get()
            ->map(fn ($income) => [
                'date' => optional($income->execution_date)->format('Y-m-d'),
                'source' => $income->budgetIncome?->name ?? 'Income',
                'amount' => number_format((float) $income->amount, 2).' '.($income->amountCurrency?->code ?? ''),
            'normalized' => number_format((float) $income->amount_normalized, 2).' '.$base,
            ]);
    }

    protected function getExpenseRows(Carbon $month)
    {
        $base = strtoupper(app(GeneralSettings::class)->default_currency);

        return Expense::query()
            ->with(['budgetSubcategory.category', 'amountCurrency'])
            ->whereBetween('execution_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->get()
            ->map(fn ($expense) => [
                'date' => optional($expense->execution_date)->format('Y-m-d'),
                'category' => sprintf(
                    '%s / %s',
                    $expense->budgetSubcategory?->category?->name ?? 'Uncategorized',
                    $expense->budgetSubcategory?->name ?? '—'
                ),
                'amount' => number_format((float) $expense->amount, 2).' '.($expense->amountCurrency?->code ?? ''),
                'normalized' => number_format((float) $expense->amount_normalized, 2).' '.$base,
            ]);
    }

    protected function getTransferRows(Carbon $month)
    {
        return Transfer::query()
            ->with(['fromAccount.amountCurrency', 'toAccount.amountCurrency'])
            ->whereBetween('created_at', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->get()
            ->map(fn ($transfer) => [
                'date' => optional($transfer->created_at)->format('Y-m-d'),
                'from' => $transfer->fromAccount?->name,
                'to' => $transfer->toAccount?->name,
                'taken' => number_format((float) $transfer->amount_taken, 2).' '.($transfer->amount_taken_currency_code ?? ''),
                'received' => number_format((float) $transfer->amount_received, 2).' '.($transfer->amount_received_currency_code ?? ''),
            ]);
    }
}
