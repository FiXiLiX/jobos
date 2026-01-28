<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Settings\GeneralSettings;
use App\Models\Currency;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        $settings = app(GeneralSettings::class);
        $defaultCurrencyCode = strtoupper($settings->default_currency);
        $activeCurrencies = $settings->active_currencies;

        $currencyCodes = array_merge([$defaultCurrencyCode], $activeCurrencies);
        $currencyCodes = array_unique(array_map('strtoupper', $currencyCodes));

        $currencyOptions = Currency::whereIn('code', $currencyCodes)
            ->pluck('name', 'id')
            ->toArray();

        $currencyCodeMap = Currency::whereIn('code', $currencyCodes)
            ->pluck('code', 'id')
            ->toArray();

        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix(fn($get) => $currencyCodeMap[$get('amount_currency_id')] ?? '$'),
                Select::make('amount_currency_id')
                    ->label('Currency')
                    ->options($currencyOptions)
                    ->searchable()
                    ->default(fn() => Currency::where('code', $defaultCurrencyCode)->first()?->id)
                    ->required()
                    ->live(),
                TextInput::make('amount_normalized')
                    ->label('Amount (Base Currency)')
                    ->disabled()
                    ->numeric()
                    ->step(0.01)
                    ->prefix($defaultCurrencyCode)
                    ->helperText('Automatically calculated based on exchange rates'),
                DatePicker::make('execution_date')
                    ->label('Execution Date')
                    ->required()
                    ->default(now()),
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable(),
                Select::make('recipient_id')
                    ->relationship('recipient', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('budget_subcategory_id')
                    ->relationship('budgetSubcategory', 'name')
                    ->required()
                    ->label('Budget Subcategory')
                    ->searchable(),
                FileUpload::make('bill_picture')
                    ->label('Bill Picture')
                    ->disk('local')
                    ->directory('livewire-tmp')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16 / 9')
                    ->imageResizeTargetWidth(1920)
                    ->imageResizeTargetHeight(1080)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->nullable()
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record && $media = $record->getFirstMedia('bill_pictures')) {
                            $component->state([$media->file_name]);
                        }
                    })
                    ->dehydrated(false)
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record) {
                        if ($record) {
                            $record->clearMediaCollection('bill_pictures');
                            $record->addMedia($file->getRealPath())
                                ->usingFileName($file->getClientOriginalName())
                                ->toMediaCollection('bill_pictures', 's3');
                        }
                        return null;
                    }),
            ]);
    }
}
