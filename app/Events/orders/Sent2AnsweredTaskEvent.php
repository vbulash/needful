<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Answer;
use App\Models\Order;
use App\Models\OrderEmployer;

class Sent2AnsweredTaskEvent extends TaskEvent {
	public function __construct(OrderEmployer $order_employer) {
		$name = $order_employer->order->getTitle();
		$employer_name = $order_employer->employer->getTitle();
		$lines = [];
		$lines[] = "Работодатель \"{$employer_name}\" готов принять практику \"{$name}\":";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrderContent($order_employer));
		$lines[] = "</ul>";
		$lines[] = "<p>Если вы готовы работать с данным работодателем - сейчас вы можете начать наполнение заявки реальными учащимися-практикантами.</p>";

		$context = [
			'order' => $order_employer->order->getKey()
		];

		parent::__construct(
		title: 'Работодатель согласился принять практику',
		description: implode("\n", $lines),
		route: route('planning.answers.index', ['order' => $context['order']]),
		from: auth()->user(),
		to: $order_employer->order->school->user,
		context: $context,
		script: null
		);
	}

	private function getOrderContent(OrderEmployer $order_employer): iterable {
		$order = $order_employer->order;
		$lines = [];
		$fields = [
			'Название учебного заведения' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Место прохождения практики' => $order->place,
			'Дополнительная информация' => $order->description,
			'Информация по специальностям заявки - наименование: количество позиций в заявке / работодатель готов принять' => null,
		];

		$employer = $order_employer->employer->getKey();
		foreach ($order->specialties as $order_specialty) {
			$answer = Answer::all()
				->where('order_specialty_id', $order_specialty->getKey())
				->where('employer_id', $employer)
				->first();
			$fields[$order_specialty->specialty->getTitle()] = sprintf("%d / %s", $order_specialty->quantity, $answer->approved ?? 'отказ');
		}

		foreach ($fields as $key => $value) {
			if ($value == null)
				$lines[] = sprintf("%s:", $key);
			else
				$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		}

		return $lines;
	}
}