<?php

namespace App\Filament\Resources\Accounts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class IncomesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';

    protected static ?string $title = 'Incomes';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetIncome.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . ($record->amountCurrency?->code ?? 'USD'))
                    ->sortable(),
                TextColumn::make('execution_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By'),
            ])
            ->defaultSort('execution_date', 'desc');
    }
}
