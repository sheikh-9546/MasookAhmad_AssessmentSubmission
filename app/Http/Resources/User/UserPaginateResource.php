<?php

namespace App\Http\Resources\User;

use App\Helpers\GlobalHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPaginateResource extends JsonResource
{
    use GlobalHelper;

    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'status'     => (bool) $this->status,
            'created_at' => $this->changeDateFormat($this->created_at, 'm/d/Y'),
            'updated_at' => $this->changeDateFormat($this->updated_at, 'm/d/Y'),
        ];
    }
}
