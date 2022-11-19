<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\TraineeStatus;
use App\Notifications\e2s\HasInternship;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class CancelWarningNotification extends Notification {
	use Queueable;
	protected History $history;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(History $history) {
		$this->history = $history;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	protected function daysLetter(int $count): string {
		$letter = ' ';
		if (($count < 10) || ($count > 20)) {
			$letter .= match ($count % 10) {
				1 => 'день',
				2, 3, 4 => 'дня',
				default => 'дней',
			};
		} else
			$letter .= 'дней';

		return $letter;
	}

	protected function checkProblems(): ?array {
		$temp = [];

		if ($this->history->status == HistoryStatus::NEW ->value)
			$temp[] = sprintf("- **Практика должна быть в статусе \"%s\"***, сейчас она в статусе \"%s\"",
				HistoryStatus::getName(HistoryStatus::PLANNED->value), HistoryStatus::getName(HistoryStatus::NEW ->value));

		$approved = $this->history->students()->wherePivot('status', TraineeStatus::APPROVED->value)->count();
		$planned = $this->history->timetable->planned;
		if ($approved != $planned)
			$temp[] = sprintf(
				"- **Количество утверждённых (%d) практикантов не равно плановому количеству практикантов, необходимых по графику практики (%d).** Можно исправить плановое количество в графике практики (привести план к факту) или добавить / удалить + утвердить практикантов в интерфейсе правки участников практики (привести факт к плану)",
				$approved, $planned);

		return count($temp) == 0 ? null : $temp;
	}

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
		$subject = sprintf("Практика начинается через %d %s", $early->cancel, $this->daysLetter($early->cancel));
		$lines = [];
		$lines[] = sprintf("Через %d %s должна начаться практика со следующими параметрами:", $early->cancel, $this->daysLetter($early->cancel));
		;
		$lines = array_merge($lines, HasInternship::getLines($this->history));
		$problems = $this->checkProblems();
		if (isset($problems)) {
			$lines[] = "Практика не сможет начаться автоматически по следующим причинам:";
			$lines = array_merge($lines, $problems);
			$lines[] = "Пожалуйста, устраните данные проблемы!";
		} else
			$lines[] = "Сейчас последний момент, когда при необходимости можно отменить практику полностью без репутационных рисков";

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
	public function toArray($notifiable) {
		return [
			//

		];
	}
}
