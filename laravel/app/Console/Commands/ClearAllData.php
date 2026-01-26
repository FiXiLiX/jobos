<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ClearAllData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear {--force : Run without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all transactional data (incomes, expenses, budgets, accounts, exchanges, etc.)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will delete all transactional data. Continue?')) {
                $this->warn('Aborted.');
                return self::SUCCESS;
            }
        }

        $tables = [
            'currency_exchanges',
            'incomes',
            'expenses',
            'budget_category_budgeteds',
            'budget_subcategory_budgeteds',
            'budget_incomes',
            'budget_subcategories',
            'budget_categories',
            'budgets',
            'accounts',
            'recipients',
        ];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                DB::table($table)->delete();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Recreate baseline budgets after wipe
            Artisan::call('budgets:ensure');
        } catch (\Throwable $e) {
            $this->error('Failed to clear data: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('All transactional data removed successfully.');

        return self::SUCCESS;
    }
}
