<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionResetSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks (MySQL / MariaDB)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all Spatie tables
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear Spatie permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
