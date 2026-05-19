<?php

namespace App\Policies\Permission;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public const MODEL = 'Permission';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const VIEW = 'view';

    public const DELETE = 'delete';

    /**
     * Only super admin  and admin can manage permissions
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdministrator();
    }

    // These methods will only be reached if user is not super admin
    public function viewAny(User $user): bool
    {
        return false;
    }

    // only super admin can view permissions
    public function view(User $user, Permission $permission): bool
    {
        return $user->isAdministrator();
    }

    // only super admin can create permissions
    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    // only super admin can update permissions
    public function update(User $user, Permission $permission): bool
    {
        return $user->isAdministrator();
    }

    // only super admin can delete permissions
    public function delete(User $user, Permission $permission): bool
    {
        return $user->isAdministrator();
    }
}
