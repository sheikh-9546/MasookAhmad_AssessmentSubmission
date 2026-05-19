<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run()
    {
        collect(['Super Admin', 'Admin', 'User'])
            ->map(function ($case) {
                Role::firstOrCreate(['name' => $case, 'slug' => Str::slug($case)]);
            });
    }
}
