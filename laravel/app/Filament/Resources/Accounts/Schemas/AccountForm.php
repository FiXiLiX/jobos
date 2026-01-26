<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;
use App\Models\Currency;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);
        $activeCurrencies = $settings->active_currencies;

        $currencyCodes = array_merge([$defaultCurrencyCode], $activeCurrencies);
        $currencyCodes = array_unique(array_map('strtoupper', $currencyCodes));

        $currencyOptions = Currency::whereIn('code', $currencyCodes)
            ->pluck('name', 'id')
            ->toArray();

        $currencyCodeMap = Currency::whereIn('code', $currencyCodes)
            ->pluck('code', 'id')
            ->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Toggle::make('on_budget')
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.01)
                    ->default(0)
                    ->prefix(fn($get) => $currencyCodeMap[$get('amount_currency_id')] ?? '$'),
                Select::make('amount_currency_id')
                    ->label('Currency')
                    ->options($currencyOptions)
                    ->searchable()
                    ->required()
                    ->live()
                    ->default(fn() => Currency::where('code', $defaultCurrencyCode)->first()?->id),
                TextInput::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->disabled()
                    ->numeric()
                    ->step(0.01)
                    ->prefix($defaultCurrencyCode)
                    ->helperText('Automatically calculated based on exchange rates'),
            ]);
    }
}
