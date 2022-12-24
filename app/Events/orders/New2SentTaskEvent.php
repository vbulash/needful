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
		$lines[] = sprintf(<<<EOD
<p>Просим принять решение по практике:</p>
<div class='d-flex mb-5'>
	<button class='btn btn-primary me-4' type='event' data-order-employer=%d>
		Да, принять практику
	</button>
</div>
EOD,
			$order_employer->getKey());
		$lines[] = '<p>Если у вас нет необходимости или возможности принять практикантов - проигнорируйте данное сообщение.</p>';

		parent::__construct(
		title: 'Ваша организация / компания приглашена для принятия практики',
		description: implode("\n", $lines),
		route: null,
		from: auth()->user(),
		to: $order_employer->employer->user,
		context: null,
		script: null
		);
	}

	private function getOrderContent(Order $order): iterable {
		$lines = [];
		$fields = [
			'Название учебного заведения' => $order->school->getTitle(),
			'Дата начала практики' => $order->start->format('d.m.Y'),
			'Дата завершения практики' => $order->end->format('d.m.Y'),
			'Дополнительная информация' => $order->description,
			'Информация по специальностям заявки - наименование: количество позиций в заявке' => null,
		];
		foreach ($order->specialties as $order_specialty) {
			$fields[$order_specialty->specialty->getTitle()] = $order_specialty->quantity;
		}

		foreach ($fields as $key => $value) {
			if ($value == null)
				$lines[] = sprintf("<li>%s:</li>", $key);
			else
				$lines[] = sprintf("<li><strong>%s</strong>: <i>%s</i></li>", $key, $value);
		}

		return $lines;
	}
}
