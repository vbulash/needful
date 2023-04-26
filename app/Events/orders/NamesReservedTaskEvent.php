<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use App\Models\OrderEmployer;

class NamesReservedTaskEvent extends TaskEvent {
	public function __construct(Answer $answer) {
		$order = $answer->orderSpecialty->order;
		$school = $order->school;
		$employer = $answer->employer;

		$lines = [];
		$lines[] = "Образовательное учреждение \"{$school->getTitle()}\" в рамках подготовки практики \"{$order->getTitle()}\":";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrder($order));
		$lines[] = "</ul>";
		$lines[] = "сообщает с извинениями, что некоторые планируемые практиканты уже выбраны для прохождения другой практики.<br/>На текущий момент список практикантов выглядит следующим образом (ФИО / специальность / статус практиканта):";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrderContent($answer));
		$lines[] = "</ul>";
		$lines[] = "<p>Мы свяжемся с вами для обсуждения других кандидатур практикантов вместо зарезервированных по другой заявке.</p>";


		$context = [
			'employer' => $employer->getKey(),
			'order' => $order->getKey(),
			'answer' => $answer->getKey(),
		];

		parent::__construct(
		title: 'Некоторые студенты забронированы для прохождения другой практики',
		description: implode("\n", $lines),
		route: route('employers.students.index'),
		from: auth()->user(),
		to: $employer->user,
		context: $context,
		script: null
		);
	}

	protected function getOrder(Order $order): iterable {
		$lines = [];
		$fields = [
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
			];
		}

		foreach ($fields as $field) {
			$lines[] = sprintf("<li><strong>%s</strong> / <em>%s</em></li>", $field['name'], $field['specialty']);
		}
		return $lines;
	}
}