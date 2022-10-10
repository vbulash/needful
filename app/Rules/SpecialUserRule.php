<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class SpecialUserRule implements Rule
{
	private Request $request;
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
    public function passes($attribute, $value): bool
    {
        $special = User::special();
		if (!$special) return true;
		if ($value != $special->getKey()) return true;

		$exists = User::where('email', $this->request->email)->first();
		return !$exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Пользователь с электронной почтой ' . $this->request->email . ' уже существует.<br/>' .
			'Вы можете либо исправить адрес электронной почты на новый либо связать анкету нового учащегося с другим пользователем без его создания';
    }
}
