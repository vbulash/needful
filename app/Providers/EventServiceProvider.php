<?php

namespace App\Providers;

use App\Events\All2DestroyedTaskEvent;
use App\Events\Asked2AcceptedTaskEvent;
use App\Events\Asked2RejectedTaskEvent;
use App\Events\InviteTraineeTaskEvent;
use App\Events\New2AskedTaskEvent;
use App\Events\orders\New2SentTaskEvent;
use App\Events\orders\Sent2AnsweredTaskEvent;
use App\Events\orders\Sent2RejectedTaskEvent;
use App\Events\UpdateEmployerTaskEvent;
use App\Events\UpdateSchoolTaskEvent;
use App\Events\UpdateStudentTaskEvent;
use App\Listeners\FilterNotificationsListener;
use App\Listeners\TaskRegisterListener;
use App\Models\Trainee;
use App\Observers\TraineeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSending;

class EventServiceProvider extends ServiceProvider {
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array<class-string, array<int, class-string>>
	 */
	protected $listen = [
		Registered::class => [
			SendEmailVerificationNotification::class,
		],
		UpdateEmployerTaskEvent::class => [
			TaskRegisterListener::class
		],
		UpdateStudentTaskEvent::class => [
			TaskRegisterListener::class
		],
		UpdateSchoolTaskEvent::class => [
			TaskRegisterListener::class
		],
		InviteTraineeTaskEvent::class => [
			TaskRegisterListener::class
		],
		Asked2AcceptedTaskEvent::class => [
			TaskRegisterListener::class
		],
		Asked2RejectedTaskEvent::class => [
			TaskRegisterListener::class
		],
		New2AskedTaskEvent::class => [
			TaskRegisterListener::class
		],
		All2DestroyedTaskEvent::class => [
			TaskRegisterListener::class
		],
			// Заявки на практику
		New2SentTaskEvent::class => [
			TaskRegisterListener::class
		],
		Sent2AnsweredTaskEvent::class => [
			TaskRegisterListener::class
		],
		Sent2RejectedTaskEvent::class => [
			TaskRegisterListener::class
		],
			// Фильтрация уведомлений перед отправкой
		NotificationSending::class => [
			FilterNotificationsListener::class
		],
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot(): void {
		Trainee::observe(TraineeObserver::class);
	}
}