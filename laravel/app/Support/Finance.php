<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

class Finance
{
    public static function netWorthAt(Carbon $date): float
    {
        // Use normalized amounts to ensure single base currency across accounts and transactions
        $base = (float) Account::all()->sum(fn ($account) => (float) $account->amount_normalized);
        $incomeSum = (float) Income::whereDate('execution_date', '<=', $date->toDateString())->sum('amount_normalized');
        $expenseSum = (float) Expense::whereDate('execution_date', '<=', $date->toDateString())->sum('amount_normalized');

        return $base + $incomeSum - $expenseSum;
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
