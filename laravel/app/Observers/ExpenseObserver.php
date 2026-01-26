<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\CurrencyExchange;
use App\Settings\GeneralSettings;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $amountInAccountCurrency = $this->convertToAccountCurrency($expense->amount_normalized, $expense->account);
        $expense->account->update([
            'amount' => $expense->account->amount - $amountInAccountCurrency
        ]);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $originalAmount = $expense->getOriginal('amount_normalized');
        $amountDifference = $expense->amount_normalized - $originalAmount;
        $amountInAccountCurrency = $this->convertToAccountCurrency($amountDifference, $expense->account);
        $expense->account->update([
            'amount' => $expense->account->amount - $amountInAccountCurrency
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $amountInAccountCurrency = $this->convertToAccountCurrency($expense->amount_normalized, $expense->account);
        $expense->account->update([
            'amount' => $expense->account->amount + $amountInAccountCurrency
        ]);
    }

    /**
     * Convert normalized amount (base currency) to account's currency
     */
    private function convertToAccountCurrency(float $normalizedAmount, $account): float
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);
        
        // If account uses base currency, no conversion needed
        if ($account->amountCurrency && strtoupper($account->amountCurrency->code) === $defaultCurrencyCode) {
            return $normalizedAmount;
        }
        
        // Get latest exchange rate for account's currency
        if ($account->amountCurrency) {
            $exchangeRate = CurrencyExchange::where('currency_id', $account->amount_currency_id)
                ->orderByDesc('exchange_date')
                ->first();
            
            if ($exchangeRate) {
                // Convert: normalized (base) * exchange_rate = account currency
                return $normalizedAmount * $exchangeRate->value;
            }
        }
        
        return $normalizedAmount;
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
