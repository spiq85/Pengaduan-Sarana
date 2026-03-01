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
        $ketuaRole = Role::where('name', 'ketua_yayasan')->first();

        $admin = User::firstOrCreate([
            'username' => 'syaviq ganteng',
            'password' => Hash::make('admin123') 
        ]);
        $admin->assignRole($adminRole);

        $ketua = User::firstOrCreate([
            'username' => 'kipas',
            'password' => Hash::make('kipas123')
        ]);
        $ketua->assignRole($ketuaRole);
    }
}
