<?php

namespace App\Notifications\orders;

use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FixNames extends Notification {
	use Queueable;
	protected Answer $answer;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Answer $answer) {
		$this->answer = $answer;
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
		$subject = 'Образовательное учреждение утвердило практикантов';

		$lines = [];
		$lines[] = "Образовательное учреждение \"{$school->getTitle()}\" в рамках подготовки практики \"{$order->getTitle()}\":";
		$lines = array_merge($lines, $this->getOrder($order));
		$lines[] = "утвердило следующих практикантов (ФИО / специальность):";
		$lines = array_merge($lines, $this->getOrderContent());

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
			];
		}

		foreach ($fields as $field) {
			$lines[] = sprintf("- **%s** / *%s*", $field['name'], $field['specialty']);
		}
		return $lines;
	}
}