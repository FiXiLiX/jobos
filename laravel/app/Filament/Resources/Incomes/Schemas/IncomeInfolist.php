<?php

namespace App\Filament\Resources\Incomes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;

class IncomeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $baseCurrencyCode = strtoupper($settings->default_currency);

        return $schema
            ->components([
                TextEntry::make('budgetIncome.name')
                    ->label('Income Category'),
                TextEntry::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . ($record->amountCurrency?->code ?? 'USD')),
                TextEntry::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->formatStateUsing(fn($state) => ($state ?? '-') . ' ' . $baseCurrencyCode),
                TextEntry::make('execution_date')
                    ->label('Execution Date')
                    ->date(),
                TextEntry::make('createdBy.name')
                    ->label('Created By'),
            ]);
    }
}
