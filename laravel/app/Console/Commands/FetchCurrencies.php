<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currencies from external API and store them in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching currencies from API...');

        $urls = [
            'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json',
            'https://currency-api.pages.dev/v1/currencies.json',
        ];

        $currencies = null;

        foreach ($urls as $url) {
            try {
                $this->info("Trying: {$url}");
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $currencies = $response->json();
                    $this->info("Successfully fetched data from: {$url}");
                    break;
                }
            } catch (\Exception $e) {
                $this->warn("Failed to fetch from {$url}: {$e->getMessage()}");
                continue;
            }
        }

        if (!$currencies) {
            $this->error('Failed to fetch currencies from all available sources.');
            return 1;
        }

        $this->info('Storing currencies in database...');
        $inserted = 0;
        $updated = 0;

        foreach ($currencies as $code => $name) {
            $code = strtoupper($code);
            
            $exists = DB::table('currencies')->where('code', $code)->exists();
            
            if ($exists) {
                DB::table('currencies')
                    ->where('code', $code)
                    ->update([
                        'name' => $name,
                        'updated_at' => now(),
                    ]);
                $updated++;
            } else {
                DB::table('currencies')->insert([
                    'code' => $code,
                    'name' => $name,
                    'exchange_rate_active' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }

        $this->info("✓ Inserted: {$inserted} currencies");
        $this->info("✓ Updated: {$updated} currencies");
        $this->info('Done!');

        return 0;
    }
}
