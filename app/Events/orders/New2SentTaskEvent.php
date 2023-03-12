<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Order;
use App\Models\OrderEmployer;

class New2SentTaskEvent extends TaskEvent {
	public function __construct(OrderEmployer $order_employer) {
		$name = $order_employer->order->getTitle();
		$lines = [];
		$lines[] = "<p>Создана заявка на практику \"{$name}\":</p>";
		$lines = array_merge($lines, $this->getOrderContent($order_employer->order));
		$lines[] = "<p>Просим принять решение по практике,  которой вы сможете изучить по ссылке ниже. Ваш ответ по практике можно также откорректировать, если у вас нет возможности принять всех предлагаемых практикантов.</p>";
		$lines[] = '<p>Если у вас нет необходимости или возможности принять практикантов - проигнорируйте данное сообщение.</p>';

		$context = [
			'employer' => $order_employer->employer->getKey(),
			'order' => $order_employer->order->getKey()
		];

		parent::__construct(
		title: 'Ваша организация / компания приглашена для принятия практики',
		description: implode("\n", $lines),
		route: route('employers.orders.answers.index', ['employer' => $context['employer'], 'order' => $context['order']]),
		from: auth()->user(),
		to: $order_employer->employer->user,
		context: $context,
		script: null
		);
	}

	private function getOrderContent(Order $order): iterable {
		$lines = [];
		$lines[] = "<ul>";
		$fields = [
			'Название образовательного учреждения' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Место прохождения практики' => $order->place,
			'Дополнительная информация' => $order->description ?? ' ',
		];
		foreach ($fields as $key => $value) {
			$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		}
		$lines[] = "</ul>";
		$lines[] = "<p>Информация по специальностям заявки - наименование: количество позиций в заявке:</p>";
		$lines[] = "<ul>";

		$fields = [];
		foreach ($order->specialties as $order_specialty) {
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity;
		}

		foreach ($fields as $key => $value) {
			$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		}
		$lines[] = "</ul>";

		return $lines;
	}
}