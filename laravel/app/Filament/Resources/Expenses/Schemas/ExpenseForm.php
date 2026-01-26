<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;
use App\Models\Currency;

class ExpenseForm
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
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix(fn($get) => $currencyCodeMap[$get('amount_currency_id')] ?? '$'),
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
                DatePicker::make('execution_date')
                    ->label('Execution Date')
                    ->required()
                    ->default(now()),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable(),
                Select::make('recipient_id')
                    ->relationship('recipient', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('budget_subcategory_id')
                    ->relationship('budgetSubcategory', 'name')
                    ->required()
                    ->label('Budget Subcategory')
                    ->searchable(),
            ]);
    }
}
