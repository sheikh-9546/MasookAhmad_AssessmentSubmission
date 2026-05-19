<?php

namespace App\Jobs\User;

use App\Enums\RoleType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Hash;

class AbstractBaseUser
{
    use Dispatchable;

    protected Role $role;

    protected User $user;

    protected function getRole(RoleType $role): static
    {
        $this->role = Role::select(['id', 'slug'])
            ->where('slug', $role->value)
            ->first();

        return $this;
    }

    protected function setAttribute($property, $value): static
    {
        $this->user->$property = $value ?? null;

        return $this;
    }

    protected function tempPassword(): static
    {
        $this->user->password = Hash::make('Password@123');

        return $this;
    }

    protected function attachRoles(): static
    {
        if ($this->role) {
            $this->user->roles()->attach($this->role->id);
        }

        return $this;
    }

    protected function createUser(): static
    {
        $this->user->save();

        return $this;
    }

    protected function get(): User
    {
        return $this->user;
    }
}
