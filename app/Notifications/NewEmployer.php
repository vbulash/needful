<?php

namespace App\Notifications;

use App\Models\ActiveStatus;
use App\Models\Employer;
use App\Models\School;
use App\Models\SchoolType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewEmployer extends Notification
{
	use Queueable;
	protected Employer $employer;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Employer $employer)
	{
		$this->employer = $employer;
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
	public function toMail($notifiable)
	{
		$admin = env('MAIL_ADMIN_ADDRESS');
		$message = (new MailMessage)
			->cc($admin)
			->subject("Создан новый работодатель")
			->line("Создан новый работодатель \"{$this->employer->name}\".")
			->lines($this->getEmployerContent())
			->line($this->getStatusWarning())
		;
		return $message;
	}

	protected function getEmployerContent(): iterable
	{
		$fields = [
			'Наименование организации' => $this->employer->name,
			'Контактное лицо' => $this->employer->contact,
			'Фактический адрес' => $this->employer->address,
			'Телефон' => $this->employer->phone,
			'Электронная почта' => $this->employer->email,
			'Индивидуальный номер налогоплательщика (ИНН)' => $this->employer->inn,
			'КПП' => $this->employer->kpp,
			'ОГРН / ОГРНИП' => $this->employer->ogrn,
			'Юридический адрес' => $this->employer->official_address,
			'Почтовый адрес' => $this->employer->post_address,
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
		$out = 'Текущий статус ("' . ActiveStatus::getName($this->employer->status) . '") ';
		$out .= match (intval($this->employer->status)) {
			ActiveStatus::NEW->value => 'ограничивает большинство операций с работодателем. ' .
				'Ограничения может снять администратор платформы после контроля корректности заполнения анкеты работодателя по следующей ссылке: ' .
				'[Карточка работодателя](' . route('employers.edit', ['employer' => $this->employer->getKey()]) . ') (предварительно необходимо авторизоваться в платформе)',
			ActiveStatus::ACTIVE->value => '- нет ограничений по выполнению операций с работодателем',
		};
		return $out;
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
