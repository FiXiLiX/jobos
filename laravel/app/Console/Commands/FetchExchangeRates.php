<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Settings\GeneralSettings;
use App\Models\CurrencyExchange;
use App\Models\Currency;

class FetchExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rates:fetch {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currency exchange rates for a specific date or today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : now();
        $dateString = $date->format('Y-m-d');
        
        $this->info("Fetching exchange rates for {$dateString}...");

        $settings = app(GeneralSettings::class);
        $baseCurrency = strtolower($settings->default_currency);
        $activeCurrencies = $settings->active_currencies;

        $this->info("Base currency: {$baseCurrency}");

        $urls = [
            "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@{$dateString}/v1/currencies/{$baseCurrency}.json",
            "https://{$dateString}.currency-api.pages.dev/v1/currencies/{$baseCurrency}.json",
        ];

        $data = null;

        foreach ($urls as $url) {
            try {
                $this->info("Trying: {$url}");
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->info("Successfully fetched data from: {$url}");
                    break;
                }
            } catch (\Exception $e) {
                $this->warn("Failed to fetch from {$url}: {$e->getMessage()}");
                continue;
            }
        }

        if (!$data) {
            $this->error('Failed to fetch exchange rates from all available sources.');
            return 1;
        }

        // Extract the rates for the base currency
        $baseCurrencyKey = strtolower($baseCurrency);
        if (!isset($data[$baseCurrencyKey])) {
            $this->error("No rates found for {$baseCurrency}");
            return 1;
        }

        $rates = $data[$baseCurrencyKey];
        $stored = 0;

        foreach ($activeCurrencies as $currencyCode) {
            // Skip the base currency itself
            if (strtolower($currencyCode) === strtolower($baseCurrency)) {
                continue;
            }

            $currencyKey = strtolower($currencyCode);
            if (!isset($rates[$currencyKey])) {
                $this->warn("No rate found for {$currencyCode}");
                continue;
            }

            $currency = Currency::where('code', strtoupper($currencyCode))->first();
            if (!$currency) {
                $this->warn("Currency {$currencyCode} not found in database");
                continue;
            }

            $rate = $rates[$currencyKey];

            CurrencyExchange::updateOrCreate(
                [
                    'currency_id' => $currency->id,
                    'exchange_date' => $date->toDateString(),
                ],
                [
                    'value' => $rate,
                ]
            );

            $stored++;
        }

        $this->info("✓ Stored {$stored} exchange rates for {$dateString}");

        return 0;
    }
}
