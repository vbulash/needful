<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateTeacher extends NewTeacher
{
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
			->subject("Изменён руководитель практики")
			->line("Изменён руководитель практики \"{$this->teacher->getTitle()}\".")
			->lines($this->getTeacherContent());
    }
}
