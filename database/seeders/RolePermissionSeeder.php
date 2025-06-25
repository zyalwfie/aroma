<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        // Daftar permission
        $permissions = [
            'view orders',
            'manage users',
            'access admin panel',
            'manage products',
            'manage reviews',
            'view reports',
        ];

        // Buat permissions dan berikan ke admin
        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm]);
            $admin->givePermissionTo($permission);
        }

        // Assign role ke user dengan ID 1
        $user = User::find(1);
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        // Assign default customer role ke user lain (opsional)
        User::where('id', '!=', 1)->get()->each(function ($user) use ($customer) {
            if (!$user->hasRole('customer')) {
                $user->assignRole('customer');
            }
        });
    }
}
