<?php

namespace App\Notifications;

use App\Models\ActiveStatus;
use App\Models\Learn;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLearn extends Notification
{
    use Queueable;
	protected Learn $learn;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Learn $learn)
    {
        $this->learn = $learn;
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
		return (new MailMessage)
			->cc($admin)
			->subject("Создана новая запись обучения")
			->line("Создана новая запись обучения \"{$this->learn->getTitle()}\".")
			->lines($this->getLearnContent())
			->line($this->getStatusWarning())
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

	protected function getLearnContent(): iterable
	{
		$fields = [
			'Дата поступления' => $this->learn->start->format('d.m.Y'),
			'Дата завершения' => $this->learn->finish ? $this->learn->finish->format('d.m.Y') : 'н/вр',
			'Учебное заведение' => $this->learn->school ? $this->learn->school->getTitle() : '(предложено) ' . $this->learn->new_school,
			'Специальность' => $this->learn->specialty ? $this->learn->specialty->getTitle() : '(предложена) ' . $this->learn->new_specialty,
		];
		$lines = [];

		foreach ($fields as $key => $value) {
			if (!isset($value)) continue;

			$lines[] = sprintf("**%s**: *%s*", $key, $value);
		}
		return $lines;
	}

	protected function getStatusWarning(): string
	{
		$out = 'Текущий статус ("' . ActiveStatus::getName($this->learn->status) . '") ';
		$out .= match (intval($this->learn->status)) {
			ActiveStatus::NEW->value => 'ограничивает большинство операций с записями истории обучения. ' .
				'Ограничения может снять администратор платформы после контроля корректности заполнения записи истории обучения по следующей ссылке: ' .
				'[Запись истории обучения](' . route('learns.edit', ['learn' => $this->learn->getKey()]) . ') (предварительно необходимо авторизоваться в платформе)',
			ActiveStatus::ACTIVE->value => '- нет ограничений по выполнению операций с записями истории обучения',
		};
		return $out;
	}
}
