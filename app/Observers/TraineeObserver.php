<?php

namespace App\Observers;

use App\Models\History;
use App\Models\Student;
use App\Models\Trainee;

class TraineeObserver
{
    public function creating(Trainee $trainee): void
	{
		$from = null;
		$to = $trainee->status;
		$history = History::findOrFail($trainee->history_id);
		$student = Student::findOrFail($trainee->student_id);
    }

    public function updating(Trainee $trainee): void
	{
		$from = $trainee->getOriginal('status');
		$to = $trainee->status;
		$history = History::findOrFail($trainee->history_id);
		$student = Student::findOrFail($trainee->student_id);
    }
}
