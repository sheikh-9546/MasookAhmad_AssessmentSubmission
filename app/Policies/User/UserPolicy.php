<?php

namespace App\Policies\User;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public const MODEL = 'User';

    public const SHOW = 'show';

    public const VIEW = 'view';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function viewAny(User $user): bool
    {
        return $user->hasAbilityTo(self::MODEL, self::VIEW);
    }

    public function view(User $user): bool
    {
        return $user->hasAbilityTo(self::MODEL, self::SHOW);
    }

    public function create(User $user): bool
    {
        return $user->hasAbilityTo(self::MODEL, self::CREATE);
    }

    public function update(User $user): bool
    {
        return $user->hasAbilityTo(self::MODEL, self::UPDATE);
    }

    public function delete(User $user): bool
    {
        return $user->hasAbilityTo(self::MODEL, self::DELETE);
    }
}
