<?php

namespace App\Notifications\e2s;

use App\Models\TraineeStatus;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Redis;

class LastWarningNotification extends CancelWarningNotification {

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		$early = Redis::get('settings.early');
		if (isset($early))
			$early = json_decode($early);
		else {
			$early = (object) [
				'cancel' => 7,
				'last' => 1
			];
			Redis::set('settings.early', json_encode($early));
		}
		$subject = sprintf("Практика начинается через %d %s", $early->last, $this->daysLetter($early->last));
		$cc = [];
		$lines = [];
		$lines[] = sprintf("Напоминаем, что через %d %s должна начаться практика со следующими параметрами:", $early->last, $this->daysLetter($early->last));
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$problems = $this->checkProblems();
		if (isset($problems)) {
			$lines[] = "Практика не сможет начаться автоматически по следующим причинам:";
			$lines = array_merge($lines, $problems);
			$lines[] = "Пожалуйста, устраните данные проблемы!";
		} else {
			foreach ($this->history->students as $student) {
				if ($student->pivot->status != TraineeStatus::APPROVED->value)
					continue;
				$cc[] = $student->user->email;
			}
			$lines[] = "Напоминаем контактные данные работодателя:";
			$employer = $this->history->timetable->internship->employer;
			$lines[] = "- **Адрес:** " . $employer->address ?? '';
			$lines[] = "- **Телефон:** " . $employer->phone ?? '';
			$lines[] = "- **Электронная почта:** " . $employer->email ?? '';

			$lines[] = 'Последние вопросы по практике вы можете задать руководителю практики:';
			$lines[] = '- **Фамилия, имя и отчество**: ' . $this->history->teacher->getTitle();
			$lines[] = '- **Телефон**: ' . $this->history->teacher->phone;
			$lines[] = '- **Электронная почта**: ' . $this->history->teacher->email;
		}

		$message = (new MailMessage)->subject($subject);
		foreach ($lines as $line)
			$message = $message->line($line);
		if (count($cc) != 0)
			$message = $message->cc($cc);

		return $message;
	}
}
