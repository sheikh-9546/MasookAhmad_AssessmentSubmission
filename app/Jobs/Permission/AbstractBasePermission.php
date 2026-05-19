<?php

namespace App\Jobs\Permission;

use App\Models\Permission;
use Illuminate\Foundation\Bus\Dispatchable;

class AbstractBasePermission
{
    use Dispatchable;

    protected Permission $permission;

    private readonly string $name;

    private readonly string $slug;

    private readonly ?string $description;

    protected function setName(): static
    {
        $this->permission->name = $this->name;

        return $this;
    }

    protected function setSlug(): static
    {
        $this->permission->slug = $this->slug;

        return $this;
    }

    protected function setDescription(): static
    {
        $this->permission->description = $this->description;

        return $this;
    }

    /**
     * Get the permission instance
     */
    protected function get(): Permission
    {
        return $this->permission;
    }
}
