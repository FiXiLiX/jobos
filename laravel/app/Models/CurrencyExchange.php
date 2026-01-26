<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyExchange extends Model
{
    protected $fillable = ['currency_id', 'value', 'exchange_date'];

    protected $casts = [
        'exchange_date' => 'date',
        'value' => 'float',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
