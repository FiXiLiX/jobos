<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year')
                    ->required(),
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('budgeted')
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.01)
                    ->default(0),
                TextInput::make('spent')
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.01)
                    ->default(0),
            ]);
    }
}
