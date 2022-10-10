<?php

namespace App\Models;

use App\Observers\TraineeObserver;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Trainee extends Pivot
{
	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = true;
}
