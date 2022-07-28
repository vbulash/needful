<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateEmployer extends NewEmployer
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
			->subject("Изменён работодатель")
			->line("Изменён работодатель \"{$this->employer->name}\".")
			->lines($this->getEmployerContent())
			->line($this->getStatusWarning())
		;
		return $message;
	}
}
