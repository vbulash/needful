<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Accepted2ApprovedNotification extends Notification
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
		$subject = 'Ваша кандидатура утверждена для прохождения практики';
		$lines = [];
		$lines[] = sprintf("Уважаемый (уважаемая) %s!", $this->student->getTitle());
		$lines[] = "Работодатель подтвердил ваше участие в практике:";
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$lines[] = "Перед началом практики с вами дополнительно свяжется руководитель практики от работодателя.";

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
