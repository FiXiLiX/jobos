<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $expense->account->update([
            'amount' => $expense->account->amount - $expense->amount_normalized
        ]);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount_normalized');
        $amountDifference = $expense->amount_normalized - $originalAmount;
        $expense->account->update([
            'amount' => $expense->account->amount - $amountDifference
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $expense->account->update([
            'amount' => $expense->account->amount + $expense->amount_normalized
        ]);
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
