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
    }
}