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

	// Учащиеся
	Route::resource('/students', 'StudentController');
	Route::get('/students.data', 'StudentController@getData')->name('students.index.data');
	Route::get('/students.select/{student}', 'StudentController@select')->name('students.select');
	// История обучения
	Route::resource('/learns', 'LearnController');
	Route::get('/learns.data', 'LearnController@getData')->name('learns.index.data');

	// Работодатели
	Route::resource('/employers', 'EmployerController');
	Route::get('/employers.data', 'EmployerController@getData')->name('employers.index.data');
	Route::get('/employers.select/{employer}', 'EmployerController@select')->name('employers.select');
	// Стажировки
	Route::resource('/internships', 'InternshipController');
	Route::get('/internships.data/{employer}', 'InternshipController@getData')->name('internships.index.data');
	Route::get('/internships.timetables/{internship}', 'InternshipController@timetables')->name('internships.timetables');
	Route::get('/internships.especialties/{internship}', 'InternshipController@especialties')->name('internships.especialties');
	// Графики стажировки
	Route::resource('/timetables', 'TimetableController');
	Route::get('/timetables.data/{internship}', 'TimetableController@getData')->name('timetables.index.data');
	// Специальности
	Route::resource('/especialties', 'EspecialtyController');
	Route::get('/especialties.data', 'EspecialtyController@getData')->name('especialties.index.data');

	// Истории стажировок
	Route::resource('/history', 'HistoryController');
	Route::get('/history.data', 'HistoryController@getData')->name('history.index.data');

	// Учебные заведения
	Route::resource('/schools', 'SchoolController');
	Route::get('/schools.data', 'SchoolController@getData')->name('schools.index.data');
	Route::get('/schools.select/{school}', 'SchoolController@select')->name('schools.select');
	// Специальности
	Route::resource('/fspecialties', 'FspecialtyController');
	Route::get('/fspecialties.data', 'FspecialtyController@getData')->name('fspecialties.index.data');

	// Руководители практики
	Route::resource('/teachers', 'TeacherController');
	Route::get('/teachers.data', 'TeacherController@getData')->name('teachers.index.data');
	Route::get('/teachers.select/{teacher}', 'TeacherController@select')->name('teachers.select');
	// Практиканты
	Route::resource('/tstudents', 'TeacherStudentController');
	Route::get('/tstudents.data', 'TeacherStudentController@getData')->name('tstudents.index.data');
	Route::get('/tstudents.source', 'TeacherStudentController@getSource')->name('tstudents.source');

	// Словари
	// Специальности
	Route::resource('/specialties', 'SpecialtyController');
	Route::get('/specialties.data', 'SpecialtyController@getData')->name('specialties.index.data');

	// Обращение к администратору платформы
	Route::post('/support', 'HelperController@support')->name('support');
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
	// Графики стажировки
	Route::get('/e2s.start_internship.step3', 'StartInternship\Step3Controller@run')->name('e2s.start_internship.step3');
	Route::get('/e2s.start_internship.step3.data', 'StartInternship\Step3Controller@getData')->name('e2s.start_internship.step3.data');
	Route::get('/e2s.start_internship.step3.show/{timetable}', 'StartInternship\Step3Controller@showTimetable')->name('e2s.start_internship.step3.show');
	Route::get('/e2s.start_internship.step3.select/{timetable}', 'StartInternship\Step3Controller@select')->name('e2s.start_internship.step3.select');
	// Практиканты
	Route::get('/e2s.start_internship.step4', 'StartInternship\Step4Controller@run')->name('e2s.start_internship.step4');
	Route::get('/e2s.start_internship.step4.data', 'StartInternship\Step4Controller@getData')->name('e2s.start_internship.step4.data');
	Route::get('/e2s.start_internship.step4.show/{student}', 'StartInternship\Step4Controller@showStudent')->name('e2s.start_internship.step4.show');
	Route::get('/e2s.start_internship.step4.select/{student}', 'StartInternship\Step4Controller@select')->name('e2s.start_internship.step4.select');
	// Подтверждение
	Route::get('/e2s.start_internship.step5', 'StartInternship\Step5Controller@run')->name('e2s.start_internship.step5');
	Route::get('/e2s.start_internship.step5.create', 'StartInternship\Step5Controller@create')->name('e2s.start_internship.step5.create');
});

require __DIR__.'/auth.php';
