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
        $review = Permission::create(['name' => 'review aspiration']);
        $approve = Permission::create(['name' => 'approve aspiration']);
        $updateProgress = Permission::create(['name' => 'update progress']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $ketua = Role::firstOrCreate(['name' => 'ketua_yayasan']);

        $admin->givePermissionTo([
            $review,
            $approve,
            $updateProgress,
        ]);

        $ketua->givePermissionTo([
            $review,
            $approve,
        ]);
    }
}
