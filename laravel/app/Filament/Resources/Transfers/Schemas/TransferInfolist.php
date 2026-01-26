<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('fromAccount.name')
                    ->label('From Account'),
                TextEntry::make('amount_taken')
                    ->label('Amount Taken')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . $record->amount_taken_currency_code),
                TextEntry::make('toAccount.name')
                    ->label('To Account'),
                TextEntry::make('amount_received')
                    ->label('Amount Received')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . $record->amount_received_currency_code),
                TextEntry::make('budgetSubcategory.name')
                    ->label('Budget Category')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
