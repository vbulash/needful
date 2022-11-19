<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class All2DestroyedNotification extends Notification
{
	use Queueable;

	private History $history;
	private Student $student;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(History $history, Student $student)
	{
		$this->history = $history;
		$this->student = $student;
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
		$subject = 'Практика отменена';
		$lines = [];
		$lines[] = sprintf("Уважаемый (уважаемая) %s!", $this->student->getTitle());
		$lines[] = "С сожалением сообщаем, что практика:";
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$lines[] = "отменена и все приглашения кандидатам в практиканты отменены.";
		$lines[] = "Мы надеемся на плодотворное сотрудничество и планируем в дальнейшем предложить вам участие в других практиках";

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
