<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SchoolSeeder extends Seeder
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

		$arrayOfPermissionNames = [
			'schools.list',
			'schools.create',
			'schools.edit',
			'schools.show',
			'schools.delete',
		];
		$permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
			return ['name' => $permission, 'guard_name' => 'web'];
		})->toArray();
		Permission::insert($permissions);

		$school = Role::where('name', 'Учебное заведение')->first();
		$school->givePermissionTo([
			'schools.create',
			'students.list',
		]);
    }
}
