<?php

namespace App\Notifications\orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class New2Sent extends Notification
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
		$subject = 'Уведомление о заявке на практику';

		$lines = [];
		$lines[] = "Создана заявка на практику \"{$name}\":";
		$lines = array_merge($lines, $this->getOrderContent());
		$lines[] = sprintf(
			"Если вы согласны принять практикантов в рамках планируемой практики, " .
			"войдите, пожалуйста, в платформу по ссылке [%s](%s), " .
			"откройте входящие сообщения и в сообщении, соответствующем данному письму, " .
			"выразите свое согласие.",
			env('APP_NAME'), env('APP_URL'));
		$lines[] = 'Если у вас нет необходимости или возможности принять практикантов - проигнорируйте данное сообщение.';

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
    public function toArray(mixed $notifiable): array
	{
        return [
            //
        ];
    }

	protected function getOrderContent(): iterable
	{
		$lines = [];
		$fields = [
			'Название учебного заведения' => $this->order->school->getTitle(),
			'Дата начала практики' => $this->order->start->format('d.m.Y'),
			'Дата завершения практики' => $this->order->end->format('d.m.Y'),
			'Место прохождения практики' => $this->order->place,
			'Дополнительная информация' => $this->order->description,
			'Информация по специальностям заявки - наименование: количество позиций в заявке' => null,
		];
		foreach ($this->order->specialties as $order_specialty)
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity;

		foreach ($fields as $key => $value) {
			if ($value == null) $lines[] = $key . ':';
			else $lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}
}
