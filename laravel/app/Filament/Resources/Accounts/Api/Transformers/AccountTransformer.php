<?php
namespace App\Filament\Resources\Accounts\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Account;

/**
 * @property Account $resource
 */
class AccountTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
