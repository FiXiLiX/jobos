<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use App\Observers\TransferObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Expense::observe(ExpenseObserver::class);
        Income::observe(IncomeObserver::class);
        Transfer::observe(TransferObserver::class);
    }
}
