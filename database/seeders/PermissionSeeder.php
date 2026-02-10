<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Updated permissions to match frontend page names
        $permissions = [
            // HR
            'Accounts.view',
            'Accounts.manage',

            // CRM
            'Clients.view',
            'Clients.manage',

            // Finance
            'Accounting.view',
            'Accounting.manage',
            'Project Financials.view',
            'Project Financials.manage',

            // SCM
            'Inventory.view',
            'Inventory.manage',
            'Suppliers.view',
            'Suppliers.manage',

            // MRP
            'Procurement.view',
            'Procurement.manage',

            // Operations
            'Projects.view',
            'Projects.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // SUPER ADMIN (YOU)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // ADMIN (CEO)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // HR
        Role::firstOrCreate(['name' => 'hr_manager'])
            ->syncPermissions([
                'Accounts.view',
                'Accounts.manage',
            ]);

        // CRM
        Role::firstOrCreate(['name' => 'cr_manager'])
            ->syncPermissions([
                'Clients.view',
                'Clients.manage',
                'Prebidding.view',
                'Prebidding.manage',
            ]);

        // Finance
        Role::firstOrCreate(['name' => 'finance_manager'])
            ->syncPermissions([
                'Accounting.view',
                'Accounting.manage',
                'Project Financials.view',
                'Project Financials.manage',
            ]);

        Role::firstOrCreate(['name' => 'accountant'])
            ->syncPermissions([
                'Accounting.view',
                'Accounting.manage',
                'Project Financials.view',
                'Project Financials.manage',
            ]);

        // SCM
        Role::firstOrCreate(['name' => 'supply_chain_manager'])
            ->syncPermissions([
                'Inventory.view',
                'Inventory.manage',
                'Suppliers.view',
                'Suppliers.manage',
            ]);

        Role::firstOrCreate(['name' => 'inventory_manager'])
            ->syncPermissions([
                'Inventory.view',
                'Inventory.manage',
            ]);

        Role::firstOrCreate(['name' => 'supplier_coordinator'])
            ->syncPermissions([
                'Suppliers.view',
                'Suppliers.manage',
            ]);

        // MRP
        Role::firstOrCreate(['name' => 'procurement_manager'])
            ->syncPermissions([
                'Procurement.view',
                'Procurement.manage',
            ]);

        // Operations
        Role::firstOrCreate(['name' => 'operations_manager'])
            ->syncPermissions([
                'Projects.view',
                'Projects.manage',
            ]);

        Role::firstOrCreate(['name' => 'project_manager'])
            ->syncPermissions([
                'Projects.view',
                'Projects.manage',
            ]);

        Role::firstOrCreate(['name' => 'site_supervisor'])
            ->syncPermissions([
                'Projects.view',
            ]);

        Role::firstOrCreate(['name' => 'foreman'])
            ->syncPermissions([
                'Projects.view',
            ]);
    }
}
