<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $roleClass = 'Spatie\Permission\Models\Role';

            if (!class_exists($roleClass)) {
                $this->command->warn('spatie/laravel-permission not installed. Skipping role seeder.');
                $this->command->warn('Run: composer require spatie/laravel-permission filament/shield');
                return;
            }

            $admin      = $roleClass::firstOrCreate(['name' => 'Admin',       'guard_name' => 'web']);
            $storekeeper = $roleClass::firstOrCreate(['name' => 'Storekeeper', 'guard_name' => 'web']);
            $roleClass::firstOrCreate(['name' => 'Viewer',      'guard_name' => 'web']);

            $permClass = 'Spatie\Permission\Models\Permission';

            $writePerms = [
                'view_product', 'view_any_product', 'create_product', 'update_product',
                'view_batch',   'view_any_batch',   'create_batch',   'update_batch',
            ];
            $readPerms = [
                'view_product', 'view_any_product',
                'view_batch',   'view_any_batch',
            ];
            $adminPerms = array_merge($writePerms, [
                'delete_product', 'restore_product', 'force_delete_product',
                'delete_batch',   'restore_batch',   'force_delete_batch',
            ]);

            foreach (array_unique(array_merge($adminPerms, $readPerms)) as $perm) {
                $permClass::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }

            $admin->syncPermissions($adminPerms);
            $storekeeper->syncPermissions($writePerms);

            $adminUser = User::where('email', 'admin@aduutech.com')->first();
            if ($adminUser) {
                $adminUser->assignRole('Admin');
            }

            $this->command->info('Roles created: Admin, Storekeeper, Viewer');
        } catch (\Throwable $e) {
            $this->command->warn('RoleSeeder failed: ' . $e->getMessage());
        }
    }
}
