<?php

use Illuminate\Support\Facades\Route;

// Утилиты
Route::group(['namespace' => 'App\Http\Controllers'], function () {
	Route::get('/test', function () {
		echo('Сервер остановлен и переведен в режим обслуживания');
	})->name('services.test');
});
