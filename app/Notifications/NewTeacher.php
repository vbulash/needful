<?php

namespace App\Notifications;

use App\Models\Employer;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTeacher extends Notification
{
    use Queueable;
	protected Teacher $teacher;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
	{
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
	{
		$admin = env('MAIL_ADMIN_ADDRESS');
		return (new MailMessage)
			->cc($admin)
			->subject("Создан новый руководитель практики")
			->line("Создан новый руководитель практики \"{$this->teacher->getTitle()}\".")
			->lines($this->getTeacherContent());
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

	protected function getTeacherContent(): iterable
	{
		$fields = [
			'ФИО руководителя практики' => $this->teacher->getTitle(),
			'Руководитель практики работает ' . match($this->teacher->job->getMorphClass()) {
				Employer::class => 'у работодателя',
				School::class => 'в учебном заведении'
			} => $this->teacher->job->getTitle(),
			'Должность руководителя практики' => $this->teacher->position,
		];
		$lines = [];

		foreach ($fields as $key => $value) {
			if (!isset($value)) continue;

			$lines[] = sprintf("**%s**: *%s*", $key, $value);
		}
		return $lines;
	}
}
