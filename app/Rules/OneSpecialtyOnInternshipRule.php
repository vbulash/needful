<?php

namespace App\Rules;

use App\Models\Internship;
use App\Models\School;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OneSpecialtyOnInternshipRule implements Rule
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
		$internship = Internship::findOrFail($context['internship']);
		$especialties = $internship->especialties->where('specialty_id', $value);

		if ($especialties->count() == 0) return true;
		if ($especialties->count() > 1) return false;
		if (!$this->request->has('id')) return false;

		if ($especialties->first()->getKey() == $this->request->id) return true;


		return false;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Такая специальность в списке специальностей практик работодателя уже есть';
	}
}
