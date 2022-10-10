<?php

namespace App\Rules;

use App\Models\ActiveStatus;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class ChangeStudentStatusRule implements Rule
{
	private Request $request;
	private User $special;

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
        if ($value != ActiveStatus::ACTIVE->value) return true;

		$this->special = User::special();
		return $this->request->user_id != $this->special->getKey();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
	{
        return "Для изменения статуса на &laquo;Активный&raquo; необходимо связать анкету учащегося с реальным пользователем, не с &laquo;{$this->special->name}&raquo;";
    }
}
