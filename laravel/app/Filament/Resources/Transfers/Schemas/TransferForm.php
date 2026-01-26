<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Account;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_account_id')
                    ->label('From Account')
                    ->relationship('fromAccount', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                TextInput::make('amount_taken_currency_code')
                    ->label('Currency (From)')
                    ->disabled()
                    ->placeholder('Auto-filled')
                    ->default(fn($get) => self::getCurrencyCode($get('from_account_id'))),
                TextInput::make('amount_taken')
                    ->label('Amount Taken')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0),
                Select::make('to_account_id')
                    ->label('To Account')
                    ->relationship('toAccount', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                TextInput::make('amount_received_currency_code')
                    ->label('Currency (To)')
                    ->disabled()
                    ->placeholder('Auto-filled')
                    ->default(fn($get) => self::getCurrencyCode($get('to_account_id'))),
                TextInput::make('amount_received')
                    ->label('Amount Received')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0),
                Select::make('budget_subcategory_id')
                    ->label('Budget Category')
                    ->relationship('budgetSubcategory', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    private static function getCurrencyCode($accountId): ?string
    {
        if (!$accountId) {
            return null;
        }

        $account = Account::with('amountCurrency')->find($accountId);
        return $account?->amountCurrency?->code;
    }
}
