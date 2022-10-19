<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Events\UnreadCountEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Models\Task;
use Illuminate\Support\Str;

class TaskRegisterListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TaskEvent $event
     * @return void
     */
    public function handle(TaskEvent $event)
    {
		$task = new Task();
		$task->uuid = Str::uuid();
		$task->title = $event->title;
		$task->description = $event->description;
		$task->route = $event->route;

		if ($event->from == null) $task->fromadmin = true;
		else $task->from()->associate($event->from);
		if ($event->to == null) $task->toadmin = true;
		else $task->to()->associate($event->to);

		$task->type = $event->type;
		$task->context = $event->context ? json_encode($event->context) : null;
		$task->script = $event->script ?? null;
		$task->save();

		event(new UnreadCountEvent());
    }
}
