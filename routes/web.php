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
	// Главная
	Route::get('/', 'MainController@index')->name('dashboard');
	// Пользователи
	Route::resource('/users', 'UserController');
	Route::get('/users.data', 'UserController@getData')->name('users.index.data');
	// Работодатели
	Route::resource('/employers', 'EmployerController');
	Route::get('/employers.data', 'EmployerController@getData')->name('employers.index.data');
	// Практиканты
	Route::resource('/students', 'StudentController');
	Route::get('/students.data', 'StudentController@getData')->name('students.index.data');
	// Стажировки
	Route::resource('/internships', 'InternshipController');
	Route::get('/internships.data/{employer}', 'InternshipController@getData')->name('internships.index.data');
	// Графики стажировки
	Route::resource('/timetables', 'TimetableController');
	Route::get('/timetables.data/{internship}', 'TimetableController@getData')->name('timetables.index.data');

	// Сервисы
	Route::get('/employers.select', 'EmployerController@select')->name('employers.select');
});

// Маршруты "от работодателя" (E2S)
Route::group(['namespace' => 'App\Http\Controllers\Services\E2S', 'middleware' => ['auth', 'restore.session']], function () {
	// Работодатели
	Route::get('/e2s.start_internship.step1', 'StartInternship\Step1Controller@run')->name('e2s.start_internship.step1');
	Route::get('/e2s.start_internship.step1.data', 'StartInternship\Step1Controller@getData')->name('e2s.start_internship.step1.data');
	Route::get('/e2s.start_internship.step1.show/{employer}', 'StartInternship\Step1Controller@showEmployer')->name('e2s.start_internship.step1.show');
	Route::get('/e2s.start_internship.step1.select/{employer}', 'StartInternship\Step1Controller@select')->name('e2s.start_internship.step1.select');
	// Стажировки
	Route::get('/e2s.start_internship.step2', 'StartInternship\Step2Controller@run')->name('e2s.start_internship.step2');
	Route::get('/e2s.start_internship.step2.data', 'StartInternship\Step2Controller@getData')->name('e2s.start_internship.step2.data');
	Route::get('/e2s.start_internship.step2.show/{internship}', 'StartInternship\Step2Controller@showInternship')->name('e2s.start_internship.step2.show');
	Route::get('/e2s.start_internship.step2.select/{internship}', 'StartInternship\Step2Controller@select')->name('e2s.start_internship.step2.select');
});

require __DIR__.'/auth.php';
