<?php

namespace App\Events\orders;

use App\Events\TaskEvent;
use App\Models\Order;
use App\Models\OrderEmployer;

class New2SentTaskEvent extends TaskEvent {
	public function __construct(OrderEmployer $order_employer) {
		$name = $order_employer->order->getTitle();
		$lines = [];
		$lines[] = "Создана заявка на практику \"{$name}\":";
		$lines[] = "<ul>";
		$lines = array_merge($lines, $this->getOrderContent($order_employer->order));
		$lines[] = "</ul>";
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