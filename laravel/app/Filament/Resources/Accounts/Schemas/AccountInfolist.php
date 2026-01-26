<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Models\Account;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;

class AccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $baseCurrencyCode = strtoupper($settings->default_currency);

        return $schema
            ->components([
                TextEntry::make('name'),
                IconEntry::make('on_budget')
                    ->boolean(),
                TextEntry::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . ($record->amountCurrency?->code ?? $baseCurrencyCode)),
                TextEntry::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->formatStateUsing(fn($state) => ($state ?? '-') . ' ' . $baseCurrencyCode),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Account $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                RepeatableEntry::make('incomes')
                    ->label('Incomes')
                    ->schema([
                        TextEntry::make('budgetIncome.name')
                            ->label('Category'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amountCurrency?->code ?? 'USD')),
                        TextEntry::make('execution_date')
                            ->label('Date')
                            ->date(),
                        TextEntry::make('createdBy.name')
                            ->label('Created By'),
                    ])
                    ->columns(4),
                RepeatableEntry::make('expenses')
                    ->label('Expenses')
                    ->schema([
                        TextEntry::make('budgetSubcategory.name')
                            ->label('Category'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amountCurrency?->code ?? 'USD')),
                        TextEntry::make('execution_date')
                            ->label('Date')
                            ->date(),
                        TextEntry::make('recipient.name')
                            ->label('Recipient'),
                    ])
                    ->columns(4),
            ]);
    }
}
