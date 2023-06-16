<?php

namespace App\Notifications;

use App\Models\Answer;
use App\Models\AnswerStatus;
use App\Models\Contract;
use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContract extends Notification {
	use Queueable;

	protected Contract $contract;
	protected bool $empty;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Contract $contract) {
		$this->contract = $contract;
		$this->empty = $this->contract->answers()->count() == 0;
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

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		// Прямой получатель - ОУ, копия - работодатель и администратор платформы
		$admin = env('MAIL_ADMIN_ADDRESS');
		$employer = $this->contract->employer->user->email;
		//
		$subject = $this->getSubject();
		//
		$lines = [];
		$lines[] = $subject . ' на проведение практики:';
		$lines = array_merge($lines, $this->getContract());
		if (!$this->empty) {
			$lines[] = 'Практиканты (специальность / ФИО):';
			$lines = array_merge($lines, $this->getStudents());
		}

		$message = (new MailMessage)
			->subject($subject)
			->cc([$admin, $employer]);
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

	protected function getContract(): iterable {
		$lines = [];
		$fields = [
			'Образовательное учреждение' => $this->contract->school->getTitle(),
			'Работодатель' => $this->contract->employer->getTitle(),
			'Номер договора' => $this->contract->number,
			'Дата заключения договора' => $this->contract->sealed->format('d.m.Y'),
			'Дата начала практики' => $this->contract->start->format('d.m.Y'),
			'Дата завершения практики' => $this->contract->finish->format('d.m.Y'),
		];
		foreach ($fields as $key => $value) {
			$lines[] = sprintf("- **%s**: *%s*", $key, $value);
		}
		return $lines;
	}

	protected function getStudents(): iterable {
		$lines = [];
		foreach ($this->contract->answers as $answer) {
			if ($answer->status != AnswerStatus::DONE->value)
				continue;
			$specialty = $answer->orderSpecialty->specialty->name;
			foreach ($answer->students as $student)
				$lines[] = sprintf("- %s / %s", $specialty, $student->getTitle());
		}
		return $lines;
	}

	protected function getSubject(): string {
		return sprintf("Зарегистрирован %sдоговор", $this->empty ? 'рамочный ' : '');
	}
}
