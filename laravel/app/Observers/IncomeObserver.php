<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\CurrencyExchange;
use App\Settings\GeneralSettings;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        if ($income->account) {
            $amountInAccountCurrency = $this->convertToAccountCurrency($income->amount_normalized, $income->account);
            $income->account->update([
                'amount' => $income->account->amount + $amountInAccountCurrency
            ]);
        }
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        $originalAmount = $income->getOriginal('amount_normalized');
        $amountDifference = $income->amount_normalized - $originalAmount;
        
        if ($income->account) {
            $amountInAccountCurrency = $this->convertToAccountCurrency($amountDifference, $income->account);
            $income->account->update([
                'amount' => $income->account->amount + $amountInAccountCurrency
            ]);
        }
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        if ($income->account) {
            $amountInAccountCurrency = $this->convertToAccountCurrency($income->amount_normalized, $income->account);
            $income->account->update([
                'amount' => $income->account->amount - $amountInAccountCurrency
            ]);
        }
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
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
        //
    }
}
