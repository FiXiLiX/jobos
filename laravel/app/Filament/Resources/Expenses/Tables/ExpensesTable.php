<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Settings\GeneralSettings;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('account.name')
                    ->searchable(),
                TextColumn::make('recipient.name')
                    ->searchable(),
                TextColumn::make('budgetSubcategory.name')
                    ->label('Budget Subcategory')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
