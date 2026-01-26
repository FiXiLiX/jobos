<?php

namespace App\Filament\Resources\Incomes\Tables;

use App\Settings\GeneralSettings;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetIncome.name')
                    ->label('Income Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($state, $record) {
                        $settings = app(GeneralSettings::class);
                        $defaultCurrencyCode = strtoupper($settings->default_currency);
                        $currencyCode = $record->amountCurrency?->code ?? $defaultCurrencyCode;
                        $formattedAmount = number_format($state, 2) . ' ' . $currencyCode;

                        if ($record->amountCurrency && strtoupper($record->amountCurrency->code) !== $defaultCurrencyCode) {
                            $formattedAmount .= ' (' . number_format($record->amount_normalized, 2) . ' ' . $defaultCurrencyCode . ')';
                        }

                        return $formattedAmount;
                    })
                    ->sortable(),
                TextColumn::make('execution_date')
                    ->label('Execution Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
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
            ->defaultSort('execution_date', 'desc');
    }
}
