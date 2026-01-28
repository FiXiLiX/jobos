<?php
namespace App\Filament\Resources\Accounts\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Accounts\AccountResource;


class AccountApiService extends ApiService
{
    protected static string | null $resource = AccountResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
