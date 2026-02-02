<?php

namespace App\Filament\Resources\Accounts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OutgoingTransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'outgoingTransfers';

    protected static ?string $title = 'Outgoing Transfers';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('toAccount.name')
                    ->label('To Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount_taken')
                    ->label('Amount Sent')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amount_taken_currency_code ?? 'USD'))
                    ->sortable(),
                TextColumn::make('amount_received')
                    ->label('Amount Received')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amount_received_currency_code ?? 'USD'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
