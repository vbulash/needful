<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Answer;
use App\Models\Order;
use App\Models\OrderEmployer;

class Sent2AnsweredTaskEvent extends TaskEvent {
	public function __construct(OrderEmployer $order_employer, ?string $message) {
		$name = $order_employer->order->getTitle();
		$employer_name = $order_employer->employer->getTitle();
		$lines = [];
		$lines[] = "Работодатель \"{$employer_name}\" готов принять практику \"{$name}\":";
		$lines = array_merge($lines, $this->getOrderContent($order_employer));

		if (isset($message)) {
			$lines[] = "<p>Работодатель оставил вам сообщение:</p>";
			$lines[] = "<ul>";
			$lines[] = "<li>{$message}</li>";
			$lines[] = "</ul>";
		}
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
		$lines[] = "<ul>";
		$fields = [
			'Название образовательного учреждения' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Место прохождения практики' => $order->place,
			'Дополнительная информация' => $order->description,
		];
		foreach ($fields as $key => $value)
			$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		$lines[] = "</ul>";
		$lines[] = '<p>Информация по специальностям заявки - наименование: количество позиций в заявке / работодатель готов принять:</p>';
		$lines[] = "<ul>";

		$fields = [];
		$employer = $order_employer->employer->getKey(); foreach ($order->specialties as $order_specialty) {
			$answer = Answer::all()
				->where('orders_specialties_id', $order_specialty->getKey())
				->where('employer_id', $employer)
				->first();
			$fields[$order_specialty->specialty->getTitle()] = sprintf("%d / %s", $order_specialty->quantity, $answer->approved > 0 ? $answer->approved : 'отказ');
		}

		foreach ($fields as $key => $value)
			$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		$lines[] = "</ul>";

		return $lines;
	}
}