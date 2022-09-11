<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateLearn extends NewLearn
{
	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail(mixed $notifiable): MailMessage
	{
		$admin = env('MAIL_ADMIN_ADDRESS');
		return (new MailMessage)
			->cc($admin)
			->subject("Изменена запись обучения")
			->line("Изменена запись обучения \"{$this->learn->getTitle()}\".")
			->lines($this->getLearnContent())
			->line($this->getStatusWarning());
	}
}
