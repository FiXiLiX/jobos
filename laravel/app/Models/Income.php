<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Settings\GeneralSettings;

class Income extends Model
{
    protected $fillable = [
        'budget_income_id',
        'amount',
        'amount_currency_id',
        'amount_normalized',
        'execution_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_normalized' => 'decimal:2',
        'execution_date' => 'date',
    ];

    public function budgetIncome(): BelongsTo
    {
        return $this->belongsTo(BudgetIncome::class);
    }

    public function amountCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'amount_currency_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // If no currency is set, use the default currency
            if (is_null($model->amount_currency_id)) {
                $defaultCurrency = Currency::where('code', app(GeneralSettings::class)->default_currency)->first();
                if ($defaultCurrency) {
                    $model->amount_currency_id = $defaultCurrency->id;
                }
            }

            // Calculate normalized amount
            $model->calculateNormalizedAmount();
        });
    }

    public function calculateNormalizedAmount()
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);

        // If amount currency is the default currency, normalized amount equals amount
        if ($this->amountCurrency && strtoupper($this->amountCurrency->code) === $defaultCurrencyCode) {
            $this->amount_normalized = $this->amount;
            return;
        }

        // Get exchange rate for the execution date
        if ($this->amountCurrency && $this->execution_date) {
            $exchangeRate = CurrencyExchange::where('currency_id', $this->amount_currency_id)
                ->where('exchange_date', $this->execution_date->toDateString())
                ->first();

            if ($exchangeRate) {
                $this->amount_normalized = $this->amount / $exchangeRate->value;
            } else {
                // If no exchange rate found, keep the amount as is
                $this->amount_normalized = $this->amount;
            }
        } else {
            $this->amount_normalized = $this->amount;
        }
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
