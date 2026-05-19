<?php

namespace App\Jobs\Permission;

use App\Models\Permission;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatePermission
{
    use Dispatchable;

    public function __construct(private readonly array $filters) {}

    public static function fromRequest($request): self
    {
        return new self($request->validated());
    }

    public function handle(): LengthAwarePaginator
    {
        $query = Permission::query();

        if (isset($this->filters['search'])) {
            $query->where('name', 'like', '%'.$this->filters['search'].'%')
                ->orWhere('slug', 'like', '%'.$this->filters['search'].'%');
        }

        $sortBy        = $this->filters['sort_by']        ?? 'id';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';
        $perPage       = $this->filters['per_page']       ?? 15;

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }
}
