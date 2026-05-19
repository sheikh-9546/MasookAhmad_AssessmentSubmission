<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionRoleSeeder extends Seeder
{
    private array $subjects = [
        'User',
    ];

    private function superAdmin(Role $role)
    {
        $role->permissions()->attach(Permission::all()->pluck('id'));
    }

    private function admin(Role $role)
    {
        // $role->permissions()->attach(Permission::whereNotIn('content_type', ['Court', 'Marshal'])->pluck('id'));
        $role->permissions()->attach(Permission::whereIn('content_type', ['User'])->whereNotIn('slug', ['create', 'delete', 'update'])->pluck('id'));

    }

    private function user(Role $role)
    {

        $role->permissions()->attach(Permission::whereIn('content_type', ['User'])->whereNotIn('slug', ['create', 'delete', 'update'])->pluck('id'));

    }

    public function run()
    {
        Role::all()
            ->map(function ($role) {
                call_user_func([$this, Str::camel($role->slug)], $role);
            });
    }
}
