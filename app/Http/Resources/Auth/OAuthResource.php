<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OAuthResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user'        => new UserResource($this->resource),
            'oauth_token' => [
                'access_token' => $this->resource
                    ->createToken(
                        $this->resource->first_name,
                        ['api:v1:token'],
                        now()->addHour(config('sanctum.expiration'))
                    )
                    ->plainTextToken,
                'expires_in' => (int) config('sanctum.expiration') * 60,
                'created_at' => Carbon::now()->getTimestampMs(),
            ],
        ];
    }
}
