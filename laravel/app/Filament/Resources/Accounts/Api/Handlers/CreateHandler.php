<?php
namespace App\Filament\Resources\Accounts\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Accounts\AccountResource;
use App\Filament\Resources\Accounts\Api\Requests\CreateAccountRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AccountResource::class;
    protected static string $permission = 'Create:Account';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Account
     *
     * @param CreateAccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateAccountRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}