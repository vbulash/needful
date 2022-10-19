<?php

namespace App\Events;

use App\Http\Controllers\Auth\RoleName;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnreadCountEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public int $count;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
    public function __construct()
    {
		$this->count = Task::getMyTasks(Task::all()->where('read', false))->count();
    }

	public function broadcastOn()
	{
		return ['needful-inbox-' . session()->getId()];
	}

	public function broadcastAs()
	{
		return 'unread-count-event';
	}
}
