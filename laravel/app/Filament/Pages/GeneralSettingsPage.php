<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use App\Settings\GeneralSettings;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Artisan;

class GeneralSettingsPage extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;
    
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    
    protected static ?string $navigationLabel = 'Settings';
    
    protected static ?int $navigationSort = 999;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearData')
                ->label('Remove All Data')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Remove All Data')
                ->modalDescription('This will delete all incomes, expenses, budgets, accounts, and exchange rates. This cannot be undone.')
                ->action(function () {
                    Artisan::call('data:clear', ['--force' => true]);

                    Notification::make()
                        ->title('All data removed')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getHeading(): string
    {
        return 'General Settings';
    }

    public function form(Schema $schema): Schema
    {
        $currencyOptions = Currency::query()
            ->pluck('name', 'code')
            ->toArray();

        // Check if there are any expenses or incomes
        $hasTransactions = Expense::exists() || Income::exists();

        return $schema
            ->components([
                Select::make('default_currency')
                    ->label('Default Currency')
                    ->required()
                    ->searchable()
                    ->options($currencyOptions)
                    ->default('USD')
                    ->disabled($hasTransactions)
                    ->helperText($hasTransactions ? 'Cannot be changed after expenses or income have been created.' : ''),
                Select::make('active_currencies')
                    ->label('Active Currencies')
                    ->multiple()
                    ->searchable()
                    ->options($currencyOptions)
                    ->default(['USD'])
            ]);
    }

    public function save(): void
    {
        $oldSettings = app(GeneralSettings::class);
        $oldActiveCurrencies = $oldSettings->active_currencies;

        parent::save();

        $newSettings = app(GeneralSettings::class);
        $newActiveCurrencies = $newSettings->active_currencies;

        // Check if active_currencies changed
        if ($oldActiveCurrencies !== $newActiveCurrencies) {
            Artisan::call('exchange-rates:fetch', ['date' => now()->toDateString()]);
        }
    }
}

