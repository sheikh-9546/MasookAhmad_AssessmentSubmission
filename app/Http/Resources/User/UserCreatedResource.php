<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCreatedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'timezone'     => $this->timezone,
            'country_code' => $this->country_code,
            'last_login'   => $this->last_login ? Carbon::parse($this->last_login)->diffForHumans() : null,
            'status'       => $this->status,
        ];
    }
}
