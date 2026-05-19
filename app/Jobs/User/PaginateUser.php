<?php

namespace App\Jobs\User;

use App\Http\Requests\User\PaginateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class PaginateUser
{
    use Dispatchable;

    public function __construct(
        private readonly int $page,
        private int $perPage,
        private ?string $search,
        private ?string $sortColumn,
        private ?string $sortDirection,

    ) {}

    public static function fromRequest(PaginateUserRequest $paginateAdminUserRequest): static
    {
        return new static(
            $paginateAdminUserRequest->getPage(),
            $paginateAdminUserRequest->getPerPage(),
            $paginateAdminUserRequest->getSearch(),
            $paginateAdminUserRequest->getSortColumn(),
            $paginateAdminUserRequest->getSortDirection(),
        );
    }

    private function paginate()
    {
        return User::orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function handle()
    {

        return $this->paginate();
    }
}
