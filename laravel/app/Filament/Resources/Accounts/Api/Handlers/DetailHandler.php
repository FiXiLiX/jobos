<?php

namespace App\Filament\Resources\Accounts\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Accounts\AccountResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Accounts\Api\Transformers\AccountTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AccountResource::class;
    protected static string $permission = 'View:Account';


    /**
     * Show Account
     *
     * @param Request $request
     * @return AccountTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new AccountTransformer($query);
    }
}
