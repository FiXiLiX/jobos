<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $baseCurrencyCode = strtoupper($settings->default_currency);

        return $schema
            ->components([
                TextEntry::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . ($record->amountCurrency?->code ?? 'USD')),
                TextEntry::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->formatStateUsing(fn($state) => ($state ?? '-') . ' ' . $baseCurrencyCode),
                TextEntry::make('execution_date')
                    ->label('Execution Date')
                    ->date(),
                TextEntry::make('account.name')
                    ->label('Account'),
                TextEntry::make('recipient.name')
                    ->label('Recipient'),
                TextEntry::make('budgetSubcategory.name')
                    ->label('Budget Subcategory'),
            ]);
    }
}
