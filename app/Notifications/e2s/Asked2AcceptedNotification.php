<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Asked2AcceptedNotification extends Notification
{
    use Queueable;

	private History $history;
	private Student $student;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(History $history, Student $student)
    {
        $this->history = $history;
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
    public function toMail(mixed $notifiable): MailMessage
	{
		$subject = 'Кандидат подтвердил участие в практике';
		$lines = [];
		$lines[] = sprintf("Кандидат \"%s\" принял решение участвовать в предлагаемой ему стажировке.", $this->student->getTitle());
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$lines[] = "Необходимо принять решение об утверждении или неутверждении данного кандидата в окончательном составе участников стажировки";

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
