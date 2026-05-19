<?php

namespace App\Http\Requests\User;

use App\Http\Requests\JsonRequest;
use Illuminate\Validation\Rule;

class PaginateUserRequest extends JsonRequest
{
    public function rules(): array
    {
        return [
            'search'         => ['sometimes', 'nullable'],
            'page'           => ['sometimes', 'nullable'],
            'per_page'       => ['sometimes', 'nullable'],
            'sort_column'    => ['sometimes', 'nullable'],
            'sort_direction' => ['sometimes', 'nullable', Rule::in(['asc', 'desc'])],
        ];
    }

    public function queryParameters(): array
    {
        return [
            'search' => [
                'description' => 'Search query term',
                'example'     => '',
            ],
            'page' => [
                'description' => 'Page count',
                'example'     => '',
            ],
            'per_page' => [
                'description' => 'Per page count',
                'example'     => '',
            ],
            'sort_column' => [
                'description' => 'Sort column',
                'example'     => '',
            ],
            'sort_direction' => [
                'description' => 'Sort direction',
                'example'     => '',
            ],
        ];
    }

    public function getSearch()
    {
        return $this->get('search');
    }

    public function getPage()
    {
        return $this->get('page', 1);
    }

    public function getPerPage()
    {
        return $this->get('per_page', 50);
    }

    public function getSortColumn()
    {
        return $this->get('sort_column', 'created_at');
    }

    public function getSortDirection()
    {
        return $this->get('sort_direction', 'desc');
    }
}
