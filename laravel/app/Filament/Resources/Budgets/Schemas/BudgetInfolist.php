<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BudgetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('year'),
                TextEntry::make('month')
                    ->numeric(),
                TextEntry::make('budgeted')
                    ->numeric(decimals: 2),
                TextEntry::make('spent')
                    ->numeric(decimals: 2),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
