<?php

namespace App\Rules;

use App\Models\History;
use App\Models\TraineeStatus;
use Illuminate\Contracts\Validation\Rule;

class PlanFactTraineesRule implements Rule
{
	private History $history;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $history)
    {
        $this->history = History::findOrFail($history);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
	{
		if ($value == $this->history->status) return true;

        $plan = $this->history->timetable->planned;
		$fact = $this->history->students()->wherePivot('status', TraineeStatus::APPROVED->value)->count();

		return $plan == $fact;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Поле &laquo;Статус&raquo; не может быть изменено, пока плановое количество и количество утверждённых практикантов не совпадают';
    }
}
