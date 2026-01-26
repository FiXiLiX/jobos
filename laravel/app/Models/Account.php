<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Settings\GeneralSettings;
use App\Models\Currency;
use App\Models\CurrencyExchange;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'on_budget',
        'amount',
        'amount_currency_id',
    ];

    protected $casts = [
        'on_budget' => 'boolean',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'amount_normalized',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Default currency if none set
            if (is_null($model->amount_currency_id)) {
                $defaultCurrency = Currency::where('code', app(GeneralSettings::class)->default_currency)->first();
                if ($defaultCurrency) {
                    $model->amount_currency_id = $defaultCurrency->id;
                }
            }
        });
    }

    public function amountCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'amount_currency_id');
    }

    public function amountNormalized(): Attribute
    {
        return Attribute::make(
            get: function () {
                $settings = app(GeneralSettings::class);
                $defaultCurrencyCode = strtoupper($settings->default_currency);

                if ($this->amountCurrency && strtoupper($this->amountCurrency->code) === $defaultCurrencyCode) {
                    return $this->amount;
                }

                if ($this->amountCurrency) {
                    $exchangeRate = CurrencyExchange::where('currency_id', $this->amount_currency_id)
                        ->orderByDesc('exchange_date')
                        ->first();

                    if ($exchangeRate) {
                        return $this->amount / $exchangeRate->value;
                    } else {
                        return $this->amount;
                    }
                } else {
                    return $this->amount;
                }
            }
        );
    }
}
