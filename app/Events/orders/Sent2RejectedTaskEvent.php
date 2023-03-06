<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Answer;
use App\Models\Order;
use App\Models\OrderEmployer;

class Sent2RejectedTaskEvent extends TaskEvent {
	public function __construct(OrderEmployer $order_employer) {
		$name = $order_employer->order->getTitle();
		$employer_name = $order_employer->employer->getTitle();
		$lines = [];
		$lines[] = "Работодатель \"{$employer_name}\" не готов принять практику \"{$name}\":";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrderContent($order_employer));
		$lines[] = "</ul>";

		parent::__construct(
		title: 'Работодатель отказался принять практику',
		description: implode("\n", $lines),
		route: null,
		from: auth()->user(),
		to: $order_employer->order->school->user,
		context: null,
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
			'Информация по специальностям заявки - наименование: количество позиций в заявке' => null,
		];

		foreach ($order->specialties as $order_specialty) {
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity;
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