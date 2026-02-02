<?php

namespace App\Filament\Resources\Incomes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use App\Models\Currency;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);
        $activeCurrencies = $settings->active_currencies;

        // Build currency options: default currency + active currencies
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
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->label('Account')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('budget_income_id')
                    ->relationship('budgetIncome', 'name')
                    ->label('Income Category')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix(fn($get) => $currencyCodeMap[$get('amount_currency_id')] ?? '$'),
                DatePicker::make('execution_date')
                    ->label('Execution Date')
                    ->required()
                    ->default(now()),
                Select::make('amount_currency_id')
                    ->label('Currency')
                    ->options($currencyOptions)
                    ->searchable()
                    ->default(fn() => Currency::where('code', $defaultCurrencyCode)->first()?->id)
                    ->required()
                    ->live(),
                TextInput::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->disabled()
                    ->numeric()
                    ->step(0.01)
                    ->prefix($defaultCurrencyCode)
                    ->helperText('Automatically calculated based on exchange rates'),
                Select::make('created_by')
                    ->relationship('createdBy', 'name')
                    ->label('Created By')
                    ->searchable()
                    ->preload()
                    ->default(Auth::id())
                    ->required(),
            ]);
    }
}
