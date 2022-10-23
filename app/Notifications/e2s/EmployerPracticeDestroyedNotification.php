<?php

namespace App\Notifications\e2s;

use App\Models\History;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerPracticeDestroyedNotification extends Notification
{
    use Queueable;

	private History $history;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(History $history)
	{
		$this->history = $history;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail(mixed $notifiable): MailMessage
	{
		$subject = 'Стажировка отменена';
		$lines = [];
		$lines[] = "Оменена стажировка со следующими параметрами:";
		$lines = array_merge($lines, HasInternship::getLines($this->history));

		$message = (new MailMessage)->subject($subject);
		foreach ($lines as $line)
			$message = $message->line($line);
		return $message;
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
    }
}
