<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            ['password' => Hash::make('admin123')]
        );

        if ($adminRole && !$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
