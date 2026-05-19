<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Name of the permission',
                'example'     => '',
            ],
            'slug' => [
                'description' => 'Slug of the permission',
                'example'     => '',
            ],
            'description' => [
                'description' => 'Description of the permission',
                'example'     => '',
            ],
        ];
    }

    public function getName(): string
    {
        return $this->get('name');
    }

    public function getSlug(): string
    {
        return $this->get('slug');
    }

    public function getDescription(): string
    {
        return $this->get('description');
    }
}
