<?php

namespace App\Filament\Resources\Accounts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = 'Expenses';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetSubcategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amountCurrency?->code ?? 'USD'))
                    ->sortable(),
                TextColumn::make('recipient.name')
                    ->label('Recipient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('execution_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('execution_date', 'desc');
    }
}
