<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
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
			// Пользователи
			'users.list',
			'users.create',
			'users.edit',
			'users.show',
			'users.delete',
			//
			'employers.list',
			'employers.create',
			'employers.edit',
			'employers.show',
			'employers.delete',
			//
			'students.list',
			'students.create',
			'students.edit',
			'students.show',
			'students.delete',
		];
		$permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
			return ['name' => $permission, 'guard_name' => 'web'];
		});
		Permission::insert($permissions->toArray());

		$employer = Role::where('name', 'Работодатель')->first();
		$employer->givePermissionTo([
			'employers.create',
			// При создании записи работодателя будет добавлены права на конкретный ID
			//'employers.edit',
			//'employers.show',
			'students.list',
		]);

		$student = Role::where('name', 'Практикант')->first();
		$student->givePermissionTo([
			'students.create',
			// При создании записи практиканта будет добавлены права на конкретный ID
			//'students.edit',
			//'students.show',
		]);
    }
}
