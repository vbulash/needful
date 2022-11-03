<?php

namespace App\Notifications\e2s;

use App\Models\HistoryStatus;
use Illuminate\Notifications\Messages\MailMessage;

class EmployerPracticeStartedNotification extends CancelWarningNotification {

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable) {
		$lines = [];
		$lines[] = "Стажировка со следующими параметрами:";
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$problems = $this->checkProblems();
		if (isset($problems)) {
			$subject = "Стажировка не может начаться сегодня";
			$lines[] = "должна была стартовать сегодня, но не сможет начаться автоматически по следующим причинам:";
			$lines = array_merge($lines, $problems);
			$lines[] = sprintf("Пожалуйста, устраните данные проблемы и вручную назначьте стажировке статус \"%s\"", HistoryStatus::getName(HistoryStatus::ACTIVE->value));
		} else {
			$this->history->update([
				'status' => HistoryStatus::ACTIVE->value
			]);
			$subject = "Стажировка началась сегодня";
			$lines[] = "автоматически стартовала сегодня";
		}

		$message = (new MailMessage)->subject($subject);
		foreach ($lines as $line)
			$message = $message->line($line);

		return $message;
	}
}
