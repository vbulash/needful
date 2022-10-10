<?php

namespace Database\Seeders;

use App\Http\Controllers\Auth\RoleName;
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

		Role::create(['name' => RoleName::ADMIN->value, 'selfassign' => false]);
		Role::create(['name' => RoleName::EMPLOYER->value, 'selfassign' => true]);
		Role::create(['name' => RoleName::TRAINEE->value, 'selfassign' => true]);
		Role::create(['name' => RoleName::SCHOOL->value, 'selfassign' => true]);
	}
}
