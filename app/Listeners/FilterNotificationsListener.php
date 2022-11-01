<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;

class FilterNotificationsListener
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
     * @param  NotificationSending  $event
     * @return bool
     */
    public function handle(NotificationSending $event): bool
    {
		$nstates = Redis::get('settings.notifications');
		if (!isset($nstates)) return true;

		$nstates = json_decode($nstates);
		return in_array(str_replace('\\', '.', get_class($event->notification)), $nstates);
    }
}
