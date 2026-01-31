<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $app_name = 'Budget App';
    public string $app_currency = 'USD';
    public string $default_currency = 'USD';
    public bool $notifications_enabled = true;
    public array $active_currencies = ['USD'];
    public int $starting_distance_counter = 0;
    public string $distance_unit = 'Kilometers';

    public static function group(): string
    {
        return 'general';
    }
}
