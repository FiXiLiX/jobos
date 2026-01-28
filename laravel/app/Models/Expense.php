<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Settings\GeneralSettings;
use App\Models\Currency;
use App\Models\CurrencyExchange;

class Expense extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'amount',
        'amount_currency_id',
        'amount_normalized',
        'execution_date',
        'account_id',
        'recipient_id',
        'budget_subcategory_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_normalized' => 'decimal:2',
        'execution_date' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('bill_pictures')
            ->singleFile();
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Default to base currency when none selected
            if (is_null($model->amount_currency_id)) {
                $defaultCurrency = Currency::where('code', app(GeneralSettings::class)->default_currency)->first();
                if ($defaultCurrency) {
                    $model->amount_currency_id = $defaultCurrency->id;
                }
            }

            $model->calculateNormalizedAmount();
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function budgetSubcategory(): BelongsTo
    {
        return $this->belongsTo(BudgetSubcategory::class);
    }

    public function amountCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'amount_currency_id');
    }

    public function calculateNormalizedAmount(): void
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);

        if ($this->amountCurrency && strtoupper($this->amountCurrency->code) === $defaultCurrencyCode) {
            $this->amount_normalized = $this->amount;
            return;
        }

        if ($this->amountCurrency && $this->execution_date) {
            $exchangeRate = CurrencyExchange::where('currency_id', $this->amount_currency_id)
                ->where('exchange_date', $this->execution_date->toDateString())
                ->first();

            if ($exchangeRate) {
                $this->amount_normalized = $this->amount / $exchangeRate->value;
            } else {
                $this->amount_normalized = $this->amount;
            }
        } else {
            $this->amount_normalized = $this->amount;
        }
    }
}
