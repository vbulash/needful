<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSpecialty extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $table = 'orders_specialties';
	protected $fillable = [
		'quantity',	// Количество позиций по специальности
	];

	public function getTitle(): string {
		return $this->specialty->getTitle();
	}

	public function order() {
		return $this->belongsTo(Order::class);
	}

	public function specialty() {
		return $this->belongsTo(Specialty::class);
	}

	public static function createTemplate(): array {
		$context = session('context');
		$order = $context['order'];
		return [
			'id' => 'order-specialty-create',
			'name' => 'order-specialty-create',
			'action' => route('order.specialties.store',['order' => $order]),
			'close' => route('order.specialties.index', ['order' => $order]),
		];
	}

	public function editTemplate(): array {
		$context = session('context');
		$order = $context['order'];
		return [
			'id' => 'order-specialty-edit',
			'name' => 'order-specialty-edit',
			'action' => route('order.specialties.update', ['order' => $order, 'specialty' => $this->getKey()]),
			'close' => route('order.specialties.index', ['order' => $order]),
		];
	}
}
