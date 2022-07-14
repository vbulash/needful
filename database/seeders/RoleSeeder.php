<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// Reset cached roles and permissions
		app()[PermissionRegistrar::class]->forgetCachedPermissions();

		Role::create(['name' => 'Администратор', 'selfassign' => false]);
		Role::create(['name' => 'Работодатель', 'selfassign' => true]);
		Role::create(['name' => 'Практикант', 'selfassign' => true]);
		Role::create(['name' => 'Учебное заведение', 'selfassign' => true]);
	}
}
