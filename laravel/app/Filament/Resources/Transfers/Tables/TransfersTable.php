<?php

namespace App\Filament\Resources\Transfers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromAccount.name')
                    ->label('From Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount_taken')
                    ->label('Amount Taken')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . $record->amount_taken_currency_code)
                    ->sortable(),
                TextColumn::make('toAccount.name')
                    ->label('To Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount_received')
                    ->label('Amount Received')
                    ->formatStateUsing(fn($state, $record) => number_format($state, 2) . ' ' . $record->amount_received_currency_code)
                    ->sortable(),
                TextColumn::make('budgetSubcategory.name')
                    ->label('Budget Category')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
