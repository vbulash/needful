<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
			'name' => 'Булаш Валерий',
			'email' => 'vbulash@yandex.ru',
			'password' => Hash::make('AeebIex1'),
		]);
		$user->assignRole('Администратор');

		$user = User::create([
			'name' => 'Компания',
			'email' => 'company@example.com',
			'password' => Hash::make('AeebIex1'),
		]);
		$user->assignRole('Работодатель');
		$user = User::create([
			'name' => 'Компания 2',
			'email' => 'company2@example.com',
			'password' => Hash::make('AeebIex1'),
		]);
		$user->assignRole('Работодатель');

		$user = User::create([
			'name' => 'Студент',
			'email' => 'student@example.com',
			'password' => Hash::make('AeebIex1'),
		]);
		$user->assignRole('Практикант');
		$user = User::create([
			'name' => 'Студент 2',
			'email' => 'student2@example.com',
			'password' => Hash::make('AeebIex1'),
		]);
		$user->assignRole('Практикант');
    }
}
