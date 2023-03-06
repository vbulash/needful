<?php

namespace App\Notifications\orders;

use App\Models\Employer;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Sent2Reject extends Notification {
	use Queueable;
	protected Order $order;
	protected Employer $employer;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Order $order, Employer $employer) {
		$this->order = $order;
		$this->employer = $employer;
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
		$name = $this->employer->getTitle();
		$subject = 'Отказ работодателя';

		$lines = [];
		$lines[] = "Работодатель \"{$name}\" отказался от участия в практике:";
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

	protected function getOrderContent(): iterable {
		$lines = [];
		$fields = [
			'Название образовательного учреждения' => $this->order->school->getTitle(),
			'Дата начала практики' => $this->order->start->format('d.m.Y'),
			'Дата завершения практики' => $this->order->end->format('d.m.Y'),
			'Место прохождения практики' => $this->order->place,
			'Дополнительная информация' => $this->order->description,
			'Информация по специальностям заявки - наименование: количество позиций в заявке' => null,
		];
		foreach ($this->order->specialties as $order_specialty)
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity; foreach ($fields as $key => $value) {
			if ($value == null)
				$lines[] = $key . ':';
			else
				$lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}
}