<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\BudgetCategoryBudgeted;
use App\Models\BudgetIncome;
use App\Models\BudgetSubcategory;
use App\Models\BudgetSubcategoryBudgeted;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Recipient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-dummy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed dummy data for budget management system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting dummy data seeding...');

        // Step 1: Remove existing data (except users)
        $this->info('Step 1: Removing existing data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Income::truncate();
        Expense::truncate();
        BudgetSubcategoryBudgeted::truncate();
        BudgetCategoryBudgeted::truncate();
        BudgetSubcategory::query()->forceDelete();
        BudgetCategory::truncate();
        BudgetIncome::query()->forceDelete();
        Budget::truncate();
        Account::truncate();
        Recipient::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('✓ Existing data removed');

        // Get first user for created_by fields
        $user = User::first();
        if (!$user) {
            $this->error('No user found. Please create a user first.');
            return 1;
        }

        // Step 2: Generate budgets
        $this->info('Step 2: Generating budgets...');
        $this->call('budget:ensure');
        $this->info('✓ Budgets generated');

        // Step 3: Add 30 random recipients
        $this->info('Step 3: Creating 30 recipients...');
        $recipientNames = [
            'Amazon', 'Walmart', 'Target', 'Costco', 'Shell Gas Station',
            'Starbucks', 'McDonald\'s', 'Whole Foods', 'Best Buy', 'Home Depot',
            'Netflix', 'Spotify', 'Apple', 'Microsoft', 'Google',
            'CVS Pharmacy', 'Walgreens', 'Kroger', 'Safeway', 'Trader Joe\'s',
            'Chipotle', 'Subway', 'Pizza Hut', 'Domino\'s', 'Panera Bread',
            'AT&T', 'Verizon', 'T-Mobile', 'Comcast', 'Electric Company'
        ];
        $recipients = [];
        foreach ($recipientNames as $name) {
            $recipients[] = Recipient::create(['name' => $name]);
        }
        $this->info('✓ 30 recipients created');

        // Step 4: Create 5 accounts
        $this->info('Step 4: Creating 5 accounts...');
        $accountNames = ['Checking Account', 'Savings Account', 'Credit Card', 'Cash', 'Investment Account'];
        $accounts = [];
        foreach ($accountNames as $name) {
            $accounts[] = Account::create([
                'name' => $name,
                'amount' => rand(100000, 1000000) / 100, // $1,000 - $10,000
                'created_by' => $user->id,
            ]);
        }
        $this->info('✓ 5 accounts created');

        // Step 5: Create 4 income categories
        $this->info('Step 5: Creating 4 income categories...');
        $incomeNames = ['Salary', 'Freelance', 'Investments', 'Other Income'];
        $incomeCategories = [];
        foreach ($incomeNames as $name) {
            $incomeCategories[] = BudgetIncome::create([
                'name' => $name,
                'created_by' => $user->id,
            ]);
        }
        $this->info('✓ 4 income categories created');

        // Step 6: Create 3 expense categories with 3-5 subcategories each
        $this->info('Step 6: Creating expense categories and subcategories...');
        $categoryData = [
            'Housing' => ['Rent/Mortgage', 'Utilities', 'Internet', 'Home Maintenance', 'Property Tax'],
            'Transportation' => ['Gas', 'Car Insurance', 'Car Maintenance', 'Public Transit'],
            'Food & Dining' => ['Groceries', 'Restaurants', 'Coffee Shops', 'Fast Food', 'Meal Delivery'],
        ];
        
        $categories = [];
        foreach ($categoryData as $categoryName => $subcategoryNames) {
            $category = BudgetCategory::create([
                'name' => $categoryName,
                'created_by' => $user->id,
            ]);
            $categories[] = $category;
            
            foreach ($subcategoryNames as $subName) {
                BudgetSubcategory::create([
                    'name' => $subName,
                    'category_id' => $category->id,
                ]);
            }
        }
        $this->info('✓ 3 categories with subcategories created');

        // Step 7: Generate expenses for current and past 2 months (3-5 per day)
        $this->info('Step 7: Generating random expenses...');
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $allSubcategories = BudgetSubcategory::all();
        $expenseCount = 0;
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dailyExpenses = rand(3, 5);
            
            for ($i = 0; $i < $dailyExpenses; $i++) {
                $subcategory = $allSubcategories->random();
                $account = $accounts[array_rand($accounts)];
                $recipient = $recipients[array_rand($recipients)];
                
                Expense::create([
                    'name' => 'Expense at ' . $recipient->name,
                    'amount' => rand(500, 50000) / 100, // $5 - $500
                    'execution_date' => $date->copy(),
                    'account_id' => $account->id,
                    'recipient_id' => $recipient->id,
                    'budget_subcategory_id' => $subcategory->id,
                    'created_by' => $user->id,
                    'created_at' => $date->copy()->setTime(rand(0, 23), rand(0, 59)),
                    'updated_at' => $date->copy()->setTime(rand(0, 23), rand(0, 59)),
                ]);
                $expenseCount++;
            }
        }
        $this->info("✓ Generated {$expenseCount} expenses");

        // Step 8: Set budgeted values for income categories (10k-20k each)
        $this->info('Step 8: Setting budgeted income values...');
        $budgets = Budget::all();
        foreach ($budgets as $budget) {
            foreach ($incomeCategories as $incomeCategory) {
                BudgetCategoryBudgeted::create([
                    'budget_id' => $budget->id,
                    'budget_income_id' => $incomeCategory->id,
                    'expected' => rand(1000000, 2000000) / 100, // $10,000 - $20,000
                    'created_by' => $user->id,
                ]);
            }
        }
        $this->info('✓ Income budgeted values set');

        // Step 9: Set budgeted values for expense subcategories
        $this->info('Step 9: Setting budgeted expense values...');
        foreach ($budgets as $budget) {
            foreach ($allSubcategories as $subcategory) {
                BudgetSubcategoryBudgeted::create([
                    'budget_id' => $budget->id,
                    'subcategory_id' => $subcategory->id,
                    'budgeted' => rand(50000, 500000) / 100, // $500 - $5,000
                ]);
            }
        }
        $this->info('✓ Expense budgeted values set');

        // Generate some actual income records for the past months
        $this->info('Step 10: Generating income records...');
        $incomeCount = 0;
        foreach ($budgets as $budget) {
            $date = Carbon::create($budget->year, $budget->month, 15); // Mid-month
            foreach ($incomeCategories as $incomeCategory) {
                if (rand(0, 100) > 30) { // 70% chance of income
                    Income::create([
                        'budget_income_id' => $incomeCategory->id,
                        'amount' => rand(900000, 1800000) / 100, // $9,000 - $18,000
                        'execution_date' => $date->copy(),
                        'created_by' => $user->id,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                    $incomeCount++;
                }
            }
        }
        $this->info("✓ Generated {$incomeCount} income records");

        // Step 11: Fetch exchange rates for all expense dates
        $this->info('Step 11: Fetching exchange rates for all expense dates...');
        $this->fetchExchangeRatesForExpenses($startDate, $endDate);

        $this->info('');
        $this->info('🎉 Dummy data seeding completed successfully!');
        $this->info('Summary:');
        $this->table(
            ['Item', 'Count'],
            [
                ['Recipients', count($recipients)],
                ['Accounts', count($accounts)],
                ['Income Categories', count($incomeCategories)],
                ['Expense Categories', count($categories)],
                ['Subcategories', $allSubcategories->count()],
                ['Budgets', $budgets->count()],
                ['Expenses', $expenseCount],
                ['Income Records', $incomeCount],
            ]
        );

        return 0;
    }

    protected function fetchExchangeRatesForExpenses(Carbon $startDate, Carbon $endDate): void
    {
        // Fetch rates for today once
        $this->call('exchange-rates:fetch', ['date' => Carbon::now()->toDateString()]);
        
        // Then copy today's rates to all dates between start and end
        $today = now()->toDateString();
        $todayRates = \App\Models\CurrencyExchange::where('exchange_date', $today)->get();
        
        if ($todayRates->isEmpty()) {
            $this->warn('No exchange rates fetched for today');
            return;
        }

        $stored = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            
            // Skip if it's today (already fetched)
            if ($dateString === $today) {
                continue;
            }

            foreach ($todayRates as $rate) {
                \App\Models\CurrencyExchange::updateOrCreate(
                    [
                        'currency_id' => $rate->currency_id,
                        'exchange_date' => $dateString,
                    ],
                    [
                        'value' => $rate->value,
                    ]
                );
                $stored++;
            }
        }

        $this->info("✓ Stored {$stored} exchange rate records for {$startDate->toDateString()} to {$endDate->toDateString()}");
    }
}