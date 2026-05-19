<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\JsonRequest;

class LoginRequest extends JsonRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'Email Address',
                'example'     => 'super@module.com',
            ],
            'password' => [
                'description' => 'password',
                'example'     => 'password',
            ],
        ];
    }

    public function getEmail(): string
    {
        return $this->get('email');
    }

    public function getCurrentPassword(): string
    {
        return $this->get('password');
    }
}
