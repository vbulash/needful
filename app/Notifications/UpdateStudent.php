<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class UpdateStudent extends NewStudent
{
	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable)
	{
		$admin = env('MAIL_ADMIN_ADDRESS');
		$message = (new MailMessage)
			->cc($admin)
			->subject("Изменён учащийся")
			->line("Изменён учащийся \"{$this->student->getTitle()}\".")
			->lines($this->getStudentContent())
			->line($this->getStatusWarning())
		;
		return $message;
	}
}
