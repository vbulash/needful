<?php

use App\Notifications\e2s\Accepted2ApprovedNotification;
use App\Notifications\e2s\Accepted2CancelledNotification;
use App\Notifications\e2s\All2DestroyedNotification;
use App\Notifications\e2s\Asked2AcceptedNotification;
use App\Notifications\e2s\Asked2CancelledNotification;
use App\Notifications\e2s\Asked2RejectedNotification;
use App\Notifications\e2s\CancelWarningNotification;
use App\Notifications\e2s\EmployerPracticeCreatedNotification;
use App\Notifications\e2s\EmployerPracticeDestroyedNotification;
use App\Notifications\e2s\LastWarningNotification;
use App\Notifications\e2s\New2AskedNotification;
use App\Notifications\NewEmployer;
use App\Notifications\NewLearn;
use App\Notifications\NewSchool;
use App\Notifications\NewStudent;
use App\Notifications\NewSupport;
use App\Notifications\NewTeacher;
use App\Notifications\NewUser;
use App\Notifications\UpdateEmployer;
use App\Notifications\UpdateLearn;
use App\Notifications\UpdateSchool;
use App\Notifications\UpdateStudent;
use App\Notifications\UpdateTeacher;

return [
	'notifications' => [
		['group' => 'Создание новых объектов', 'classes' => [
			NewEmployer::class => 'Новый работодатель',
			NewLearn::class => 'Новая запись обучения',
			NewSchool::class => 'Новое учебное заведение',
			NewStudent::class => 'Новый учащийся',
			NewTeacher::class => 'Новый руководитель практики',
			NewUser::class => 'Новый пользователь',
		]],
		['group' => 'Обновление существующих объектов', 'classes' => [
			UpdateEmployer::class => 'Обновление работодателя',
			UpdateLearn::class => 'Обновление записи обучения',
			UpdateSchool::class => 'Обновление учебного заведения',
			UpdateStudent::class => 'Обновление учащегося',
			UpdateTeacher::class => 'Обновление руководителя практики',
		]],
		['group' => 'Изменения состояний приглашения на практику', 'classes' => [
			New2AskedNotification::class => 'Новое приглашение -> Кандидат интересен',
			Asked2AcceptedNotification::class => 'Кандидат интересен -> Кандидат подтвердил',
			Asked2RejectedNotification::class => 'Кандидат интересен -> Кандидат отказался',
			Asked2CancelledNotification::class => 'Кандидат интересен -> Приглашение отменено',
			Accepted2ApprovedNotification::class => 'Кандидат подтвердил -> Кандидат утверждён',
			Accepted2CancelledNotification::class => 'Кандидат подтвердил -> Кандидат отменён',
			All2DestroyedNotification::class => 'Стажировка отменена (уведомление кандидатам)',
		]],
		['group' => 'События стажировки', 'classes' => [
			EmployerPracticeCreatedNotification::class => 'Стажировка создана',
			CancelWarningNotification::class => 'Предварительное уведомление о стажировке',
			LastWarningNotification::class => 'Последнее напоминание перед началом стажировки',
			EmployerPracticeDestroyedNotification::class => 'Стажировка отменена (уведомление работодателю)',
		]],
		['group' => 'Разное', 'classes' => [
			NewSupport::class => 'Пользователь отправил сообщение администратору',
		]],
	]
];
