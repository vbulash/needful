<?php

namespace App\Notifications;

use App\Models\ActiveStatus;
use App\Models\School;
use App\Models\SchoolType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewSchool extends Notification
{
    use Queueable;
	protected School $school;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(School $school)
    {
        $this->school = $school;
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
		$type = Str::lower(SchoolType::getName($this->school->type));

		$admin = env('MAIL_ADMIN_ADDRESS');
        $message = (new MailMessage)
			->cc($admin)
			->subject("Создано новое учебное заведение")
			->line("Создано новое учебное заведение \"{$this->school->name}\" ({$type}).")
			->lines($this->getSchoolContent())
			->line($this->getStatusWarning())
			;
		return $message;
    }

	protected function getSchoolContent(): iterable
	{
		$fields = [
			"Наименование учебного заведения" => $this->school->name,
			"Статус активности объекта" => ActiveStatus::getName($this->school->status),
			"Контактное лицо" => $this->school->contact,
			"Телефон" => $this->school->phone,
			"Электронная почта" => $this->school->email,
			"ИНН" => $this->school->inn,
			"КПП" => $this->school->kpp,
			"ОГРН / ОГРНИП" => $this->school->ogrn,
			"Юридический адрес" => $this->school->official_address,
			"Почтовый адрес" => $this->school->post_address,
		];
		$lines = [];

		foreach ($fields as $key => $value) {
			if (!isset($value)) continue;

			$lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}

	protected function getStatusWarning(): string
	{
		$out = 'Текущий статус ("' . ActiveStatus::getName($this->school->status) . '") ';
		$out .= match (intval($this->school->status)) {
			ActiveStatus::NEW->value => 'ограничивает большинство операций с учебным заведением. ' .
				'Ограничения может снять администратор платформы после контроля корректности заполнения анкеты учебного заведения по следующей ссылке: ' .
				'[Карточка учебного заведения](' . route('schools.edit', ['school' => $this->school->getKey()]) . ') (предварительно необходимо авторизоваться в платформе)',
			ActiveStatus::ACTIVE->value => '- нет ограничений по выполнению операций с учебным заведением',
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
