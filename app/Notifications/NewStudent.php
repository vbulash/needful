<?php

namespace App\Notifications;

use App\Models\ActiveStatus;
use App\Models\Employer;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudent extends Notification
{
    use Queueable;
	protected Student $student;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Student $student)
	{
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
	public function toMail($notifiable)
	{
		$admin = env('MAIL_ADMIN_ADDRESS');
		$message = (new MailMessage)
			->cc($admin)
			->subject("Создан новый учащийся")
			->line("Создан новый учащийся \"{$this->student->getTitle()}\".")
			->lines($this->getStudentContent())
			->line($this->getStatusWarning())
		;
		return $message;
	}

	protected function getStudentContent(): iterable
	{
		$fields = [
			'Фамилия' => $this->student->lastname,
			'Имя' => $this->student->firstname,
			'Отчество' => $this->student->surname,
			'Пол' => $this->student->sex,
			'Дата рождения' => $this->student->birthdate->format('d.m.Y'),
			'Телефон' => $this->student->phone,
			'Электронная почта' => $this->student->email,
			'ФИО родителей (до 14 лет)' => $this->student->parents,
			'Контактные телефоны родителей или опекунов' => $this->student->parentscontact,
			'Данные документа, удостоверяющего личность (серия, номер, кем и когда выдан)' => $this->student->passport,
			'Адрес проживания' => $this->student->address,
			'Класс / группа (на момент заполнения)' => $this->student->grade,
			'Увлечения (хобби)' => $this->student->hobby,
			'Как давно занимаетесь хобби (лет)?' => $this->student->hobbyyears,
			'Участие в конкурсах, олимпиадах. Достижения' => $this->student->contestachievements,
			'Чем хочется заниматься в жизни?' => $this->student->dream,
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
		$out = 'Текущий статус ("' . ActiveStatus::getName($this->student->status) . '") ';
		$out .= match (intval($this->student->status)) {
			ActiveStatus::NEW->value => 'ограничивает большинство операций с учащимся. ' .
				'Ограничения может снять администратор платформы после контроля корректности заполнения анкеты учащегося по следующей ссылке: ' .
				'[Карточка учащегося](' . route('students.edit', ['student' => $this->student->getKey()]) . ') (предварительно необходимо авторизоваться в платформе)',
			ActiveStatus::ACTIVE->value => '- нет ограничений по выполнению операций с учащимся',
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
