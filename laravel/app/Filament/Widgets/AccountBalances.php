<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountBalances extends ApexChartWidget
{
    protected static ?string $chartId = 'accountBalances';

    protected static ?string $heading = 'Account Balances';

    protected function getOptions(): array
    {
        $accounts = Account::query()
            ->with('amountCurrency')
            ->get()
            ->map(function (Account $account) {
                return [
                    'name' => $account->name,
                    'normalized' => $account->amount_normalized,
                    'currency' => $account->amountCurrency?->code ?? 'N/A',
                ];
            });

        $labels = $accounts->pluck('name')->toArray();
        $data = [];
        $colors = [];

        foreach ($accounts as $account) {
            $value = round((float)$account['normalized'], 2);
            $data[] = $value;
            $colors[] = $value >= 0 ? '#3b82f6' : '#ef4444'; // Blue for positive, red for negative
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 320,
                'distributed' => true,
            ],
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Normalized Balance (Base Currency)',
                    'data' => $data,
                ],
            ],
            'colors' => $colors,
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'distributed' => true,
                    'dataLabels' => [
                        'position' => 'top',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'offsetY' => -20,
            ],
            'legend' => [
                'show' => false,
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => null,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 1;
    }
}
