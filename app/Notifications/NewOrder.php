<?php

namespace App\Notifications;

use App\Models\ActiveStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrder extends Notification
{
    use Queueable;
	protected Order $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable)
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
		$admin = env('MAIL_ADMIN_ADDRESS');
		$name = $this->order->getTitle();
		return (new MailMessage)
			->cc($admin)
			->subject("Создана новая заявка на практику")
			->line("Создана новая заявка на практику \"{$name}\".")
			->lines($this->getOrderContent())
		;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
	{
        return [
            //
        ];
    }

	protected function getOrderContent(): iterable
	{
		$fields = [
			'Название учебного заведения' => $this->order->school->getTitle(),
			'Дата начала практики' => $this->order->start->format('d.m.Y'),
			'Дата завершения практики' => $this->order->end->format('d.m.Y'),
			'Населённый пункт прохождения практики' => $this->order->place,
			'Дополнительная информация' => $this->order->description,
			'Информация по специальностям заявки - наименование: количество позиций в заявке' => null,
		];
		foreach ($this->order->specialties as $order_specialty)
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity;

		$fields['Информация по уведомлениям работодателей (список работодателей)'] = null;
		foreach ($this->order->employers as $order_employer)
			$fields[$order_employer->employer->getTitle()] = '*';
		$lines = [];

		foreach ($fields as $key => $value) {
			if ($value == '*') $lines[] = sprintf("- **%s**", $key);
			elseif ($value == null) $lines[] = $key . ':';
			else $lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}
}
