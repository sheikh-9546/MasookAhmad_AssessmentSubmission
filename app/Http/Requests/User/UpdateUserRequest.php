<?php

namespace App\Http\Requests\User;

use App\Http\Requests\JsonRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends JsonRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            // 'email'   => ['required', 'email', 'max:100',Rule::unique('users')->ignore($this->user->id)->where('deleted_at',null)],
            'phone'   => ['nullable'],
            'country' => ['required_with:phone', Rule::in(['US', 'IN'])],
        ];

    }

    public function bodyParameters(): array
    {
        return [
            'first_name' => [
                'description' => 'Firstname of the user',
                'example'     => '',
            ],
            'last_name' => [
                'description' => 'Lastname of the user',
                'example'     => '',
            ],
            'phone' => [
                'description' => 'Phone number of the user',
                'example'     => '',
            ],
            'country' => [
                'description' => 'Country code of the user',
                'example'     => 'IN',
            ],
        ];
    }

    public function getFirstName(): string
    {
        return $this->get('first_name');
    }

    public function getLastName(): string
    {
        return $this->get('last_name');
    }

    public function getPhone(): ?string
    {
        return $this->get('phone');
    }

    public function getCountry(): string
    {
        return $this->get('country');
    }
}
