<?php

namespace App\Jobs\Permission;

use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Models\Permission;
use Illuminate\Foundation\Bus\Dispatchable;

class CreatePermission extends AbstractBasePermission
{
    use Dispatchable;

    public function __construct(
        private readonly string $name,
        private readonly string $slug,
        private readonly ?string $description
    ) {}

    public static function fromRequest(CreatePermissionRequest $createPermissionRequest): self
    {
        return new self(
            $createPermissionRequest->getName(),
            $createPermissionRequest->getSlug(),
            $createPermissionRequest->getDescription()
        );
    }

    protected function initializePermission(): static
    {
        $this->permission = new Permission;

        return $this;
    }

    protected function save(): static
    {
        $this->permission->save();

        return $this;
    }

    public function handle()
    {
        return $this->initializePermission()
            ->setName()
            ->setSlug()
            ->setDescription()
            ->save()
            ->get();
    }
}
