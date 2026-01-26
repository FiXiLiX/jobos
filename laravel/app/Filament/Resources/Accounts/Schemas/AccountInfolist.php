<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Models\Account;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
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
            ]);
    }
}
