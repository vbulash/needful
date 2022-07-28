<?php

namespace App\Rules;

use App\Models\Fspecialty;
use App\Models\School;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OneSpecialtyOnSchoolRule implements Rule
{
	protected Request $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
		if ($this->request->has('specialty') && isset($this->request->specialty)) return true;

		$context = session('context');
        $school = School::findOrFail($context['school']);
		$fspecialties = $school->fspecialties->where('specialty_id', $value);

		if ($fspecialties->count() == 0) return true;
		if ($fspecialties->count() > 1) return false;
		if (!$this->request->has('id')) return false;

		if ($fspecialties->first()->getKey() == $this->request->id) return true;


		return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Такая специальность в списке специальностей учебного заведения уже есть';
    }
}
