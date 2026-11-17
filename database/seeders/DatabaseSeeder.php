<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['admin', 'staff', 'customer'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@labsupply.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['admin']);

        User::updateOrCreate(
            ['email' => 'staff@labsupply.id'],
            [
                'name' => 'Staff Lab Supply',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->syncRoles(['staff']);

        $this->call([
            CatalogSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
