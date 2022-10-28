<?php

return [
	'all' => [
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
		// Стажировки
		'histories.list',
		'histories.create',
		'histories.edit',
		'histories.show',
		'histories.delete',
	],
	'map' => [
		// Работодатели
		['role' => 'employer', 'title' => 'Работодатель', 'permissions' => [
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
			// Стажировки
			'histories.list',
			'histories.create',
			'histories.edit',
			'histories.show',
			'histories.delete',
		]],
		// Учебные заведения
		['role' => 'school', 'title' => 'Учебное заведение', 'permissions' => [
			// Пользователи
			'users.show',
			// Работодатели
			'employers.list',
			'employers.show',
			// Учащиеся
			'students.list',
			'students.show',
			// Стажировки
			'histories.list',
			'histories.create',
			'histories.edit',
			'histories.show',
		]],
		// Учащиеся
		['role' => 'student', 'title' => 'Учащийся', 'permissions' => [
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
		]],
	],
];
