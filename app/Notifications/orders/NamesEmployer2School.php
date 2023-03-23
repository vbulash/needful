<?php

namespace App\Notifications\orders;

use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NamesEmployer2School extends Notification {
	use Queueable;
	protected Answer $answer;
	protected ?string $message;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Answer $answer, ?string $message) {
		$this->answer = $answer;
		$this->message = $message;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via(mixed $notifiable) {
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail(mixed $notifiable): MailMessage {
		$admin = env('MAIL_ADMIN_ADDRESS');
		$order = $this->answer->orderSpecialty->order;
		$employer = $this->answer->employer;
		$school = $order->school;
		$subject = 'Работодатель дал обратную связь по практикантам';

		$lines = [];
		$lines[] = "Работодатель  \"{$employer->getTitle()}\" в рамках подготовки практики \"{$order->getTitle()}\":";
		$lines = array_merge($lines, $this->getOrder($order));
		$lines[] = "дал обратную связь по практикантам (ФИО / специальность / статус практиканта):";
		$lines = array_merge($lines, $this->getOrderContent());
		if (isset($this->message)) {
			$lines[] = "Работодатель дополнительно оставил вам сообщение:";
			$lines[] = "- *{$this->message}*";
		}

		$message = (new MailMessage)
			->subject($subject)
			->cc($admin);
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
	public function toArray(mixed $notifiable): array {
		return [
			//
		];
	}

	protected function getOrder(Order $order): iterable {
		$lines = [];
		$fields = [
			'Образовательное учреждение' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Место прохождения практики' => $order->place,
		];
		if (isset($order->description))
			$fields['Дополнительная информация'] = $order->description;

		foreach ($fields as $key => $value) {
			$lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}

	protected function getOrderContent(): iterable {
		$lines = [];
		$specialty = $this->answer->orderSpecialty->specialty;

		$fields = [];
		foreach ($this->answer->students as $student) {
			$fields[] = [
				'name' => $student->getTitle(),
				'specialty' => $specialty->name,
				'status' => AnswerStudentStatus::getName($student->pivot->status)
			];
		}

		foreach ($fields as $field) {
			$lines[] = sprintf("- **%s** / *%s* / *%s*", $field['name'], $field['specialty'], $field['status']);
		}
		return $lines;
	}
}