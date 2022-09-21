<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use App\Models\TraineeStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerPracticeNotification extends Notification
{
    use Queueable;

	private History $history;
	private Student $trainee;
	private int $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Student $trainee, History $history, int $status)
    {
        $this->history = $history;
		$this->trainee = $trainee;
		$this->status = $status;
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
    public function toMail(mixed $notifiable): MailMessage
	{
		$subject = '';
		$lines = [];
		$lines[] = 'Уважаемый (уважаемая) ' . $this->trainee->getTitle(). '!';
		switch ($this->status) {
			case TraineeStatus::ASKED->value:
				$subject = 'Запланирована новая практика';
				$lines[] = 'Вас пригласили для прохождения практики. Информация по данной практике:';
				break;
			case TraineeStatus::ACCEPTED->value:
				$subject = 'Вы подтвердили участие в практике';
				$lines[] = 'Напоминаем параметры данной практики:';
				break;
			case TraineeStatus::REJECTED->value:
				$subject = 'Вы отказались от участия в практике';
				$lines[] = 'Напоминаем параметры данной практики:';
				break;
		}
		$lines[] = 'Работодатель: "' . $this->history->timetable->internship->employer->getTitle() . '"';
		$lines[] = 'Стажировка: "' . $this->history->timetable->internship->getTitle() . '"';
		$lines[] = 'График стажировки: ' . $this->history->timetable->getTitle();

		switch ($this->status) {
			case TraineeStatus::ASKED->value:
				$lines[] = 'Пройдите, пожалуйста, по ссылке ... чтобы выразить свое согласие или несогласие с данным приглашением.';
				$lines[] = 'В случае игнорирования приглашения через 10 дней оно будет аннулировано';
				break;
			default:
				;
		}

		$message = (new MailMessage)->subject($subject);
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
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
