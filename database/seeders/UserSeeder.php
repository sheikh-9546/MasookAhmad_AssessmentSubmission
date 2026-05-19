<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $createdUser = User::create([
            'first_name'                  => 'Super',
            'last_name'                   => 'Admin',
            'email'                       => 'super@module.com',
            'password'                    => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  // password
            'status'                      => 1,
            'default_password_changed_at' => now(),
        ]);
        $createdUser->roles()->attach(Role::where('slug', 'super-admin')->pluck('id'));
        User::factory()->count(app()->environment('local') ? 10 : 10)->create();
    }
}
