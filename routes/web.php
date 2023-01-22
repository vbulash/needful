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
	Route::get('/employers.clear.index', 'EmployerController@getClear')->name('employers.index.clear');
	Route::get('/employers.data', 'EmployerController@getData')->name('employers.index.data');
	Route::get('/employers.select/{employer}', 'EmployerController@select')->name('employers.select');
	Route::get('/employers.select2/{employer}', 'EmployerController@select2')->name('employers.select2');
	// Заявка на практику для работодателя
	Route::get('/employers.orders/{employer}', 'EmployerOrderController@index')->name('employers.orders.index');
	Route::get('/employers.orders.data/{employer}', 'EmployerOrderController@getData')->name('employers.orders.index.data');
	Route::post('/employers.orders.cancel', 'EmployerOrderController@cancel')->name('employers.orders.cancel');

	// Практики
	Route::resource('/internships', 'InternshipController');
	Route::get('/internships.data/{employer}', 'InternshipController@getData')->name('internships.index.data');
	Route::get('/internships.timetables/{internship}', 'InternshipController@timetables')->name('internships.timetables');
	Route::get('/internships.especialties/{internship}', 'InternshipController@especialties')->name('internships.especialties');
	// Графики практики
	Route::resource('/timetables', 'TimetableController');
	Route::get('/timetables.data/{internship}', 'TimetableController@getData')->name('timetables.index.data');
	// Специальности
	Route::resource('/especialties', 'EspecialtyController');
	Route::get('/especialties.data', 'EspecialtyController@getData')->name('especialties.index.data');
	// Специальности работодателя (новая ветка)
	Route::resource('/employer.specialties', 'EmployerSpecialtyController');
	Route::get('/employers.specialties.data/{employer}', 'EmployerSpecialtyController@getData')->name('employer.specialties.index.data');

	// Истории практики
	Route::resource('/history', 'HistoryController');
	Route::get('/history.data', 'HistoryController@getData')->name('history.index.data');
	Route::get('/history.select/{history}', 'HistoryController@select')->name('history.select');
	Route::post('/history.can.destroy', 'HistoryController@canDestroy')->name('history.can.destroy');
	Route::post('/history.cancel', 'HistoryController@cancel')->name('history.cancel');
	// Практиканты
	Route::get('/trainees', 'TraineeController@index')->name('trainees.index');
	Route::get('/trainees.data', 'TraineeController@getData')->name('trainees.index.data');
	Route::get('/trainees/{trainee}', 'TraineeController@show')->name('trainees.show');
	Route::post('/trainees.link', 'TraineeController@link')->name('trainees.link');
	Route::post('/trainees.unlink', 'TraineeController@unlink')->name('trainees.unlink');
	Route::post('/trainees.invite.all', 'TraineeController@inviteAll')->name('trainees.invite.all');
	Route::post('/trainees.transition', 'TraineeController@transition')->name('trainees.transition');

	// Учебные заведения
	Route::resource('/schools', 'SchoolController');
	Route::get('/schools.data', 'SchoolController@getData')->name('schools.index.data');
	Route::get('/schools.select/{school}', 'SchoolController@select')->name('schools.select');
	// Специальности
	Route::resource('/fspecialties', 'FspecialtyController');
	Route::get('/fspecialties.data', 'FspecialtyController@getData')->name('fspecialties.index.data'); // Заявки на практику от учебного заведения
	// Route::get('/schools.orders', '')

	// Заявки на практику
	Route::resource('/orders', 'OrderController');
	Route::get('/orders.data', 'OrderController@getData')->name('orders.index.data');
	Route::get('/orders.select/{order}', 'OrderController@select')->name('orders.select');
	// Специальности в заявке
	Route::resource('/order.specialties', 'OrderSpecialtyController');
	Route::get('/orders.specialties.data/{order}', 'OrderSpecialtyController@getData')->name('order.specialties.index.data');
	// Уведомления работодателям в заявке
	Route::resource('/order.employers', 'OrderEmployerController');
	Route::get('/orders.employers.data/{order}', 'OrderEmployerController@getData')->name('order.employers.index.data');
	Route::post('/orders.employers.mail', 'OrderEmployerController@mail')->name('order.employers.mail');

	// Руководители практики
	Route::resource('/teachers', 'TeacherController');
	Route::get('/teachers.data', 'TeacherController@getData')->name('teachers.index.data');

	// Почтовый ящик
	Route::get('/inbox', 'InboxController@index')->name('inbox.index');
	Route::get('/archive', 'InboxController@archive')->name('inbox.archive');

	// Словари
	// Специальности
	Route::resource('/specialties', 'SpecialtyController');
	Route::get('/specialties.data', 'SpecialtyController@getData')->name('specialties.index.data');

	// Обращение к администратору платформы
	Route::post('/support', 'HelperController@support')->name('support');

	// Сообщения
	Route::post('/message.read', 'TaskController@read')->name('message.read');
	Route::get('/message.link', 'TaskController@link')->name('message.link');
	Route::get('/message.archive', 'TaskController@archive')->name('message.archive');
	Route::post('/message.dispatcher', 'TaskController@dispatcher')->name('message.dispatcher');
	Route::delete('/message', 'TaskController@destroy')->name('message.destroy');

	// Импорт
	Route::get('/import', 'ImportController@index')->name('import.index');
	Route::get('/import.errors', 'ImportController@errors')->name('import.errors');
	// Импорт учащихся
	Route::get('/import.students.create', 'Imports\StudentImportController@create')->name('import.students.create');
	Route::get('import.students.download', 'Imports\StudentImportController@download')->name('import.students.download');
	Route::get('import.students.download.specialties', 'Imports\StudentImportController@downloadSpecialties')->name('import.students.download.specialties');
	Route::post('import.students.upload', 'Imports\StudentImportController@upload')->name('import.students.upload');

	// Настройки
	Route::get('/settings.notifications', 'SettingsController@notifications')->name('settings.notifications');
	Route::post('/settings.notifications.store', 'SettingsController@notificationsStore')->name('settings.notifications.store');
	Route::get('/settings.early.warnings', 'EarlyWarningsController@warnings')->name('settings.early');
	Route::put('/settings.early.warnings.store', 'EarlyWarningsController@warningsStore')->name('settings.early.store');
});

// Маршруты "от работодателя" (E2S)
Route::group(['namespace' => 'App\Http\Controllers\Services\E2S', 'middleware' => ['auth', 'restore.session']], function () {
	// Работодатели
	Route::get('/e2s.start_internship.step1', 'StartInternship\Step1Controller@run')->name('e2s.start_internship.step1');
	Route::get('/e2s.start_internship.step1.data', 'StartInternship\Step1Controller@getData')->name('e2s.start_internship.step1.data');
	Route::get('/e2s.start_internship.step1.show/{employer}', 'StartInternship\Step1Controller@showEmployer')->name('e2s.start_internship.step1.show');
	Route::get('/e2s.start_internship.step1.select/{employer}', 'StartInternship\Step1Controller@select')->name('e2s.start_internship.step1.select');
	// Практики
	Route::get('/e2s.start_internship.step2', 'StartInternship\Step2Controller@run')->name('e2s.start_internship.step2');
	Route::get('/e2s.start_internship.step2.data', 'StartInternship\Step2Controller@getData')->name('e2s.start_internship.step2.data');
	Route::get('/e2s.start_internship.step2.show/{internship}', 'StartInternship\Step2Controller@showInternship')->name('e2s.start_internship.step2.show');
	Route::get('/e2s.start_internship.step2.select/{internship}', 'StartInternship\Step2Controller@select')->name('e2s.start_internship.step2.select');
	// Графики практики
	Route::get('/e2s.start_internship.step3', 'StartInternship\Step3Controller@run')->name('e2s.start_internship.step3');
	Route::get('/e2s.start_internship.step3.data', 'StartInternship\Step3Controller@getData')->name('e2s.start_internship.step3.data');
	Route::get('/e2s.start_internship.step3.show/{timetable}', 'StartInternship\Step3Controller@showTimetable')->name('e2s.start_internship.step3.show');
	Route::get('/e2s.start_internship.step3.select/{timetable}', 'StartInternship\Step3Controller@select')->name('e2s.start_internship.step3.select');
	// Практиканты
	Route::get('/e2s.start_internship.step4', 'StartInternship\Step4Controller@run')->name('e2s.start_internship.step4');
	Route::get('/e2s.start_internship.step4.data', 'StartInternship\Step4Controller@getData')->name('e2s.start_internship.step4.data');
	Route::get('/e2s.start_internship.step4.show/{student}', 'StartInternship\Step4Controller@showStudent')->name('e2s.start_internship.step4.show');
	Route::post('/e2s.start_internship.step4.select', 'StartInternship\Step4Controller@select')->name('e2s.start_internship.step4.select');
	// Руководитель практики
	Route::get('/e2s.start_internship.step4b', 'StartInternship\Step4bController@run')->name('e2s.start_internship.step4b');
	Route::post('/e2s.start_internship.step4b.select', 'StartInternship\Step4bController@select')->name('e2s.start_internship.step4b.select');
	// Подтверждение
	Route::get('/e2s.start_internship.step5', 'StartInternship\Step5Controller@run')->name('e2s.start_internship.step5');
	Route::get('/e2s.start_internship.step5.create', 'StartInternship\Step5Controller@create')->name('e2s.start_internship.step5.create');
});

// Заявки на практику
Route::group(['namespace' => 'App\Http\Controllers\orders', 'middleware' => ['auth']], function () {
	Route::get('/orders.steps/play', 'StepController@play')->name('orders.steps.play');
	Route::get('/orders.steps/back', 'StepController@back')->name('orders.steps.back');
	Route::get('/orders.steps/next', 'StepController@next')->name('orders.steps.next');
	Route::get('/orders.steps/close', 'StepController@close')->name('orders.steps.close');
	Route::get('/orders.steps/finish', 'StepController@finish')->name('orders.steps.finish');
	Route::get('/orders.steps.data', 'StepController@getData')->name('orders.steps.index.data');
});

require __DIR__.'/auth.php';
