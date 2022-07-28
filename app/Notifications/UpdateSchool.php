<?php

namespace App\Notifications;

use App\Models\SchoolType;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class UpdateSchool extends NewSchool
{
	public function toMail($notifiable)
	{
		$type = Str::lower(SchoolType::getName($this->school->type));

		$admin = env('MAIL_ADMIN_ADDRESS');
		$message = (new MailMessage)
			->cc($admin)
			->subject("Изменено учебное заведение")
			->line("Изменено учебное заведение \"{$this->school->name}\" ({$type}).")
			->lines($this->getSchoolContent())
			->line($this->getStatusWarning())
		;
		return $message;
	}

}
