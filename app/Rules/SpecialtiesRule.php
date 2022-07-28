<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SpecialtiesRule implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
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
		$arr = json_decode($value);
		if (!is_array($arr)) return false;
		return count($arr) > 0;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Список специальностей не может быть пустым';
	}
}
