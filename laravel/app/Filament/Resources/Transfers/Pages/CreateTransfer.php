<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set currency codes from the selected accounts
        $fromAccount = \App\Models\Account::with('amountCurrency')->find($data['from_account_id']);
        $toAccount = \App\Models\Account::with('amountCurrency')->find($data['to_account_id']);

        $data['amount_taken_currency_code'] = $fromAccount?->amountCurrency?->code ?? 'USD';
        $data['amount_received_currency_code'] = $toAccount?->amountCurrency?->code ?? 'USD';

        return $data;
    }
}
