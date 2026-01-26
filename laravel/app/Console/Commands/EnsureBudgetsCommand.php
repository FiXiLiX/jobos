<?php

namespace App\Console\Commands;

use App\Models\Budget;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class EnsureBudgetsCommand extends Command
{
    protected $signature = 'budgets:ensure';

    protected $description = 'Ensure budgets exist for current month, previous 3 months, and next 12 months.';

    public function handle(): int
    {
        $start = Carbon::now()->startOfMonth();

        foreach (range(-3, 12) as $offset) {
            $monthDate = $start->copy()->addMonthsNoOverflow($offset);

            $exists = Budget::where('year', $monthDate->year)
                ->where('month', $monthDate->month)
                ->exists();

            if ($exists) {
                continue;
            }

            Budget::create([
                'year' => $monthDate->year,
                'month' => $monthDate->month,
            ]);

            $this->info("Created budget for {$monthDate->format('Y-m')}");
        }

        return self::SUCCESS;
    }
}
