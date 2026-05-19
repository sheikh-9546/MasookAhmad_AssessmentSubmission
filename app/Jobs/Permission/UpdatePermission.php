<?php

namespace App\Jobs\Permission;

use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePermission extends AbstractBasePermission
{
    use Dispatchable;

    public function __construct(
        protected readonly string $name,
        protected readonly string $slug,
        protected readonly ?string $description,
        protected readonly Permission $permission
    ) {}

    public static function fromRequest(UpdatePermissionRequest $updatePermissionRequest, Permission $permission): self
    {
        return new self(
            $updatePermissionRequest->getName(),
            $updatePermissionRequest->getSlug(),
            $updatePermissionRequest->getDescription(),
            $permission
        );
    }

    protected function update(): self
    {
        $this->permission->save();

        return $this;
    }

    public function handle(): Permission
    {
        return $this->setName()
            ->setSlug()
            ->setDescription()
            ->update()
            ->get();
    }
}
