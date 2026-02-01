<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

class Finance
{
    /**
     * Get current net worth (sum of all account balances)
     */
    public static function currentNetWorth(): float
    {
        // amount_normalized is an accessor, not a DB column, so we must load all accounts
        return (float) Account::all()->sum(fn($account) => (float) $account->amount_normalized);
    }

    /**
     * Get net worth at a specific date
     * For current month: sum of all account balances
     * For past months: current balance - incomes after that date + expenses after that date
     */
    public static function netWorthAt(Carbon $date): float
    {
        $currentNetWorth = self::currentNetWorth();
        
        // If the date is in the current month or future, return current net worth
        if ($date->gte(Carbon::now()->startOfMonth())) {
            return $currentNetWorth;
        }
        
        // For past months, we need to reverse transactions that happened after the target date
        // Subtract incomes that happened after this date (they increased our balance)
        $incomesAfter = (float) Income::whereDate('execution_date', '>', $date->toDateString())
            ->sum('amount_normalized');
        
        // Add back expenses that happened after this date (they decreased our balance)
        $expensesAfter = (float) Expense::whereDate('execution_date', '>', $date->toDateString())
            ->sum('amount_normalized');

        return $currentNetWorth - $incomesAfter + $expensesAfter;
    }

    public static function netWorthSeries(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $data = [];

        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('M Y');
            $data[] = (float) round(self::netWorthAt($cursor->copy()->endOfMonth()), 2);
            $cursor->addMonth();
        }

        return [$labels, $data];
    }
}
