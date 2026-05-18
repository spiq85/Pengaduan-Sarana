<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $review = Permission::firstOrCreate(['name' => 'review aspiration']);
        $approve = Permission::firstOrCreate(['name' => 'approve aspiration']);
        $updateProgress = Permission::firstOrCreate(['name' => 'update progress']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $admin->givePermissionTo([
            $review,
            $approve,
            $updateProgress,
        ]);

        Role::where('name', 'ketua_yayasan')->delete();
    }
}
