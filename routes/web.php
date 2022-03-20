<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth', 'restore.session']], function () {
	Route::get('/', function () {
		return view('layouts.backend');
	})->name('dashboard');

	// Пользователи
	Route::resource('/users', 'UserController');
	Route::get('/users.data', 'UserController@getData')->name('users.index.data');
	// Работодатели
	Route::resource('/employers', 'EmployerController');
	Route::get('/employers.data', 'EmployerController@getData')->name('employers.index.data');
});

require __DIR__.'/auth.php';
