<?php

namespace Database\Seeders;

use App\Http\Controllers\Auth\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		// Reset cached roles and permissions
		app()[PermissionRegistrar::class]->forgetCachedPermissions();

		$arrayOfPermissionNames = [
			// Пользователи
			'users.list',
			'users.create',
			'users.edit',
			'users.show',
			'users.delete',
			// Работодатели
			'employers.list',
			'employers.create',
			'employers.edit',
			'employers.show',
			'employers.delete',
			// Учащиеся
			'students.list',
			'students.create',
			'students.edit',
			'students.show',
			'students.delete',
			// Учебные заведения
			'schools.list',
			'schools.create',
			'schools.edit',
			'schools.show',
			'schools.delete',
			// Практики
			'histories.list',
			'histories.create',
			'histories.edit',
			'histories.show',
			'histories.delete',
		];
		$permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
			return ['name' => $permission, 'guard_name' => 'web'];
		});
		Permission::insert($permissions->toArray());

		$employer = Role::where('name', RoleName::EMPLOYER->value)->first();
		$employer->givePermissionTo([
			// Пользователи
			'users.show',
			// Работодатели
			'employers.list',
			'employers.create',
			'employers.edit',
			'employers.show',
			// Учащиеся
			'students.list',
			'students.show',
			// Практики
			'histories.list',
			'histories.create',
			'histories.edit',
			'histories.show',
			'histories.delete',
		]);

		$school = Role::where('name', RoleName::SCHOOL->value)->first();
		$school->givePermissionTo([
			// Пользователи
			'users.show',
			// Работодатели
			'employers.list',
			'employers.show',
			// Учащиеся
			'students.list',
			'students.show',
			// Практики
			'histories.list',
			'histories.create',
			'histories.edit',
			'histories.show',
		]);

		$student = Role::where('name', RoleName::TRAINEE->value)->first();
		$student->givePermissionTo([
			// Пользователи
			'users.show',
			// Работодатели
			'employers.list',
			'employers.show',
			// Учащиеся
			'students.list',
			'students.create',
			'students.edit',
			'students.show',
			'students.delete',
			// Практики
			'histories.list',
			'histories.show',
		]);
	}
}
