<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CurrencyExchange;
use Carbon\Carbon;

class CurrencyExchangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default exchange rates as of 2026-01-28
        // Base currency: RSD (Serbian Dinar)
        $rates = [
            'USD' => 105.50,  // 1 RSD = 0.0094 USD (1 USD = 105.50 RSD)
            'EUR' => 114.25,  // 1 RSD = 0.0087 EUR (1 EUR = 114.25 RSD)
            'GBP' => 130.75,  // 1 RSD = 0.0076 GBP (1 GBP = 130.75 RSD)
            'CHF' => 117.50,  // 1 RSD = 0.0085 CHF (1 CHF = 117.50 RSD)
            'JPY' => 0.720,   // 1 RSD = 0.72 JPY (1 JPY = 1.39 RSD)
            'AUD' => 68.50,   // 1 RSD = 0.0146 AUD (1 AUD = 68.50 RSD)
            'CAD' => 74.25,   // 1 RSD = 0.0135 CAD (1 CAD = 74.25 RSD)
        ];

        $date = Carbon::now();

        foreach ($rates as $currencyCode => $rate) {
            // Check if rate already exists for today
            $exists = CurrencyExchange::where('currency_id', function ($query) use ($currencyCode) {
                $query->select('id')->from('currencies')->where('code', $currencyCode)->limit(1);
            })->where('exchange_date', $date->toDateString())->exists();

            if (!$exists) {
                $currency = \App\Models\Currency::where('code', $currencyCode)->first();
                if ($currency) {
                    CurrencyExchange::create([
                        'currency_id' => $currency->id,
                        'value' => $rate,
                        'exchange_date' => $date,
                    ]);
                }
            }
        }

        $this->command->info('Currency exchange rates seeded successfully!');
    }
}
