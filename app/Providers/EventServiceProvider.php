<?php

namespace App\Providers;

use App\Events\Asked2AcceptedTaskEvent;
use App\Events\Asked2RejectedTaskEvent;
use App\Events\InviteTraineeTaskEvent;
use App\Events\New2AskedTaskEvent;
use App\Events\UpdateEmployerTaskEvent;
use App\Events\UpdateSchoolTaskEvent;
use App\Events\UpdateStudentTaskEvent;
use App\Listeners\TaskRegisterListener;
use App\Models\Trainee;
use App\Observers\TraineeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
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
		]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
	{
		Trainee::observe(TraineeObserver::class);
    }
}
