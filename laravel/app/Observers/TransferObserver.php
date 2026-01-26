<?php

namespace App\Observers;

use App\Models\Transfer;
use App\Models\CurrencyExchange;
use App\Settings\GeneralSettings;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        // Reload to ensure relationships are loaded
        $transfer->load('fromAccount.amountCurrency', 'toAccount.amountCurrency');

        // Deduct amount_taken from from_account
        if ($transfer->fromAccount) {
            $amountToDeduct = $this->convertToAccountCurrency(
                $transfer->amount_taken,
                $transfer->amount_taken_currency_code,
                $transfer->fromAccount->amountCurrency?->code ?? 'USD',
                $transfer->created_at
            );
            $transfer->fromAccount->decrement('amount', $amountToDeduct);
        }

        // Add amount_received to to_account
        if ($transfer->toAccount) {
            $amountToAdd = $this->convertToAccountCurrency(
                $transfer->amount_received,
                $transfer->amount_received_currency_code,
                $transfer->toAccount->amountCurrency?->code ?? 'USD',
                $transfer->created_at
            );
            $transfer->toAccount->increment('amount', $amountToAdd);
        }
    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        if ($transfer->wasChanged(['amount_taken', 'amount_received', 'from_account_id', 'to_account_id'])) {
            // Reload to ensure relationships are loaded
            $transfer->load('fromAccount.amountCurrency', 'toAccount.amountCurrency');

            // Get original values
            $originalFromAccountId = $transfer->getOriginal('from_account_id');
            $originalToAccountId = $transfer->getOriginal('to_account_id');
            $originalAmountTaken = $transfer->getOriginal('amount_taken');
            $originalAmountReceived = $transfer->getOriginal('amount_received');
            $originalTakenCurrency = $transfer->getOriginal('amount_taken_currency_code');
            $originalReceivedCurrency = $transfer->getOriginal('amount_received_currency_code');

            // Reverse old amounts from original accounts
            $oldFromAccount = Transfer::find($transfer->id)->where('id', $transfer->id)->first() 
                ? \App\Models\Account::find($originalFromAccountId) 
                : null;
            
            if ($oldFromAccount) {
                $oldAmountToDeduct = $this->convertToAccountCurrency(
                    $originalAmountTaken,
                    $originalTakenCurrency,
                    $oldFromAccount->amountCurrency?->code ?? 'USD',
                    $transfer->created_at
                );
                $oldFromAccount->increment('amount', $oldAmountToDeduct);
            }

            $oldToAccount = \App\Models\Account::find($originalToAccountId);
            if ($oldToAccount) {
                $oldAmountToAdd = $this->convertToAccountCurrency(
                    $originalAmountReceived,
                    $originalReceivedCurrency,
                    $oldToAccount->amountCurrency?->code ?? 'USD',
                    $transfer->created_at
                );
                $oldToAccount->decrement('amount', $oldAmountToAdd);
            }

            // Apply new amounts
            if ($transfer->fromAccount) {
                $amountToDeduct = $this->convertToAccountCurrency(
                    $transfer->amount_taken,
                    $transfer->amount_taken_currency_code,
                    $transfer->fromAccount->amountCurrency?->code ?? 'USD',
                    $transfer->created_at
                );
                $transfer->fromAccount->decrement('amount', $amountToDeduct);
            }

            if ($transfer->toAccount) {
                $amountToAdd = $this->convertToAccountCurrency(
                    $transfer->amount_received,
                    $transfer->amount_received_currency_code,
                    $transfer->toAccount->amountCurrency?->code ?? 'USD',
                    $transfer->created_at
                );
                $transfer->toAccount->increment('amount', $amountToAdd);
            }
        }
    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        // Reload to ensure relationships are loaded
        $transfer->load('fromAccount.amountCurrency', 'toAccount.amountCurrency');

        // Reverse the transaction - add back to from_account
        if ($transfer->fromAccount) {
            $amountToAdd = $this->convertToAccountCurrency(
                $transfer->amount_taken,
                $transfer->amount_taken_currency_code,
                $transfer->fromAccount->amountCurrency?->code ?? 'USD',
                $transfer->created_at
            );
            $transfer->fromAccount->increment('amount', $amountToAdd);
        }

        // Remove from to_account
        if ($transfer->toAccount) {
            $amountToRemove = $this->convertToAccountCurrency(
                $transfer->amount_received,
                $transfer->amount_received_currency_code,
                $transfer->toAccount->amountCurrency?->code ?? 'USD',
                $transfer->created_at
            );
            $transfer->toAccount->decrement('amount', $amountToRemove);
        }
    }

    /**
     * Convert amount from source currency to target account currency using exchange rates
     */
    private function convertToAccountCurrency(
        $amount,
        $sourceCurrencyCode,
        $targetCurrencyCode,
        $date
    ) {
        $settings = app(GeneralSettings::class);
        $baseCurrencyCode = strtoupper($settings->default_currency);
        
        // If source and target are the same, no conversion needed
        if (strtoupper($sourceCurrencyCode) === strtoupper($targetCurrencyCode)) {
            return $amount;
        }

        // If target is base currency, convert from source to base
        if (strtoupper($targetCurrencyCode) === $baseCurrencyCode) {
            $sourceCurrency = \App\Models\Currency::where('code', strtoupper($sourceCurrencyCode))->first();
            if ($sourceCurrency) {
                $exchange = CurrencyExchange::where('currency_id', $sourceCurrency->id)
                    ->whereDate('exchange_date', $date->toDateString())
                    ->first() ?? CurrencyExchange::where('currency_id', $sourceCurrency->id)
                        ->orderByDesc('exchange_date')
                        ->first();
                
                if ($exchange) {
                    return $amount / $exchange->value;
                }
            }
            return $amount;
        }

        // Convert source to base, then base to target
        // First: source to base
        $amountInBase = $amount;
        if (strtoupper($sourceCurrencyCode) !== $baseCurrencyCode) {
            $sourceCurrency = \App\Models\Currency::where('code', strtoupper($sourceCurrencyCode))->first();
            if ($sourceCurrency) {
                $exchange = CurrencyExchange::where('currency_id', $sourceCurrency->id)
                    ->whereDate('exchange_date', $date->toDateString())
                    ->first() ?? CurrencyExchange::where('currency_id', $sourceCurrency->id)
                        ->orderByDesc('exchange_date')
                        ->first();
                
                if ($exchange) {
                    $amountInBase = $amount / $exchange->value;
                }
            }
        }

        // Then: base to target
        $targetCurrency = \App\Models\Currency::where('code', strtoupper($targetCurrencyCode))->first();
        if ($targetCurrency) {
            $exchange = CurrencyExchange::where('currency_id', $targetCurrency->id)
                ->whereDate('exchange_date', $date->toDateString())
                ->first() ?? CurrencyExchange::where('currency_id', $targetCurrency->id)
                    ->orderByDesc('exchange_date')
                    ->first();
            
            if ($exchange) {
                return $amountInBase * $exchange->value;
            }
        }

        return $amountInBase;
    }
}
