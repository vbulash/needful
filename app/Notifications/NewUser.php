<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
		$roles = $notifiable->getRoleNames()->join(",<br/>");
        return (new MailMessage)
			->subject("Создан новый пользователь" )
			->line("Создан новый пользователь \"{$notifiable->name}\" с ролью \"{$roles}\".")
			->line("В целях безопасности пароль пользователя никогда не пересылается по почте. " .
				sprintf("Если вы забудете свой пароль или вам его не передавали - воспользуйтесь функцией его сброса на диалоге входа в \"%s\" (%s) и получите дальнейшие инструкции в электронной почте.",
					env('APP_NAME'), env('APP_URL')));
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
