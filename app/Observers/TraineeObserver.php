<?php

namespace App\Observers;

use App\Models\Trainee;

class TraineeObserver
{
    /**
     * Handle the Trainee "created" event.
     *
     * @param Trainee $trainee
     * @return void
     */
    public function created(Trainee $trainee): void
	{
        $test = $trainee;
    }

    /**
     * Handle the Trainee "updated" event.
     *
     * @param Trainee $trainee
     * @return void
     */
    public function updated(Trainee $trainee): void
	{
        $test = $trainee;
    }

    /**
     * Handle the Trainee "deleted" event.
     *
     * @param Trainee $trainee
     * @return void
     */
    public function deleted(Trainee $trainee): void
	{
        //
    }

    /**
     * Handle the Trainee "restored" event.
     *
     * @param Trainee $trainee
     * @return void
     */
    public function restored(Trainee $trainee): void
	{
        //
    }

    /**
     * Handle the Trainee "force deleted" event.
     *
     * @param Trainee $trainee
     * @return void
     */
    public function forceDeleted(Trainee $trainee): void
	{
        //
    }
}
