<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionAddSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. DEFINE NEW PERMISSIONS HERE
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'Prebidding.view',
            'Prebidding.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. DEFINE ROLES + PERMISSIONS
        |--------------------------------------------------------------------------
        | givePermissionTo() ADDS permissions
        | syncPermissions() REPLACES permissions
        */
        $roles = [
            'cr_manager' => [
                'Prebidding.view',
                'Prebidding.manage',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. SUPER ADMIN GETS EVERYTHING (SAFE)
        |--------------------------------------------------------------------------
        */
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $user = User::find(37);
        $user->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
