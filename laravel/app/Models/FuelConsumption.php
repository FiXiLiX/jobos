<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelConsumption extends Model
{
    protected $fillable = [
        'fill_date',
        'fill_amount',
        'fill_price',
        'current_distance_counter',
    ];

    protected $casts = [
        'fill_date' => 'date',
    ];
}
