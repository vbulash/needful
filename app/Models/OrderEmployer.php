<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEmployer extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $table = 'orders_employers';

	protected $fillable = [
		'status',	// Статус информирования работодателя
	];

	public function getTitle(): string {
		return $this->employer->getTitle();
	}

	public function order(): BelongsTo {
		return $this->belongsTo(Order::class);
	}

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}

	public static function createTemplate(): array {
		$context = session('context');
		$order = $context['order'];
		return [
			'id' => 'order-employer-create',
			'name' => 'order-employer-create',
			'action' => route('order.employers.store', ['order' => $order]),
			'close' => route('order.employers.index', ['order' => $order]),
		];
	}

	public function editTemplate(): array {
		$context = session('context');
		$order = $context['order'];
		return [
			'id' => 'order-employer-edit',
			'name' => 'order-employer-edit',
			'action' => route('order.employers.update', ['order' => $order, 'employer' => $this->getKey()]),
			'close' => route('order.employers.index', ['order' => $order]),
		];
	}
}
