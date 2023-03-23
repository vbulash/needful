<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use App\Models\OrderEmployer;

class NamesBack2SchoolTaskEvent extends TaskEvent {
	public function __construct(Answer $answer, ?string $message) {
		$order = $answer->orderSpecialty->order;
		$school = $order->school;
		$employer = $answer->employer;

		$lines = [];
		$lines[] = "<p>Работодатель \"{$employer->getTitle()}\" в рамках подготовки практики:</p>";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrder($order));
		$lines[] = "</ul>";
		$lines[] = "<p>дал обратную связь по практикантам (ФИО / специальность / статус практиканта):</p>";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrderContent($answer));
		$lines[] = "</ul>";
		// $lines[] = "<p>По ссылке внизу данного сообщения вы сможете либо полностью одобрить предложение образовательного учреждения, либо работать с практикантами индиввидуально - принять или отклонить.</p>";
		if (isset($message)) {
			$lines[] = "<p>Работодатель оставил вам сообщение:</p>";
			$lines[] = "<ul>";
			$lines[] = "<li>{$message}</li>";
			$lines[] = "</ul>";
		}

		$context = [
			'order' => $order->getKey(),
			'answer' => $answer->getKey(),
		];

		parent::__construct(
		title: 'Работодатель ответил по практикантам',
		description: implode("\n", $lines),
		route: route('planning.students.index'),
		from: auth()->user(),
		to: $school->user,
		context: $context,
		script: null
		);
	}

	protected function getOrder(Order $order): iterable {
		$lines = [];
		$fields = [
			'Образовательное учреждение' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Место прохождения практики' => $order->place,
		];
		if (isset($order->description))
			$fields['Дополнительная информация'] = $order->description;

		foreach ($fields as $key => $value) {
			$lines[] = sprintf("<li><strong>%s</strong>: %s</li>", $key, $value);
		}
		return $lines;
	}

	private function getOrderContent(Answer $answer): iterable {
		$lines = [];
		$specialty = $answer->orderSpecialty->specialty;

		$fields = [];
		foreach ($answer->students as $student) {
			$fields[] = [
				'name' => $student->getTitle(),
				'specialty' => $specialty->name,
				'status' => AnswerStudentStatus::getName($student->pivot->status)
			];
		}

		foreach ($fields as $field) {
			$lines[] = sprintf("<li><strong>%s</strong> / <em>%s</em> / <em>%s<em></li>", $field['name'], $field['specialty'], $field['status']);
		}
		return $lines;
	}
}