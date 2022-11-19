<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class New2AskedNotification extends Notification {
	use Queueable;

	private History $history;
	private Student $student;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(History $history, Student $student) {
		$this->history = $history;
		$this->student = $student;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail(mixed $notifiable): MailMessage {
		$subject = 'Приглашение на практику';
		$lines = [];
		$lines[] = sprintf("Уважаемый (уважаемая) %s!", $this->student->getTitle());
		$lines[] = "Вас пригласили для прохождения практику. Информация по данной практике:";
		$lines = array_merge($lines, HasInternship::getLines($this->history));

		$lines[] = 'Все вопросы по практике вы можете задать руководителю практики:';
		$lines[] = '- **Фамилия, имя и отчество**: ' . $this->history->teacher->getTitle();
		$lines[] = '- **Телефон**: ' . $this->history->teacher->phone;
		$lines[] = '- **Электронная почта**: ' . $this->history->teacher->email;

		$lines[] = sprintf(
			"Войдите, пожалуйста в платформу по ссылке [%s](%s). " .
			"Откройте входящие сообщения и в сообщении, соответствующем данному письму, " .
			"выразите свое согласие или несогласие участию в практике.",
			env('APP_NAME'), env('APP_URL'));
		$lines[] = 'В случае игнорирования приглашения через 10 дней оно будет аннулировано';

		$message = (new MailMessage)
			->subject($subject)
			->cc($this->history->teacher->email);
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
	public function toArray($notifiable) {
		return [
			//

		];
	}
}
