<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

/**
 * Заявка на практику
 *
 * @property string $name
 * @property DateTime $start
 * @property DateTime $end
 */
class Order extends Model implements FormTemplate
{
    use HasFactory, GrantedAll;

	protected $fillable = [
		'name',		// Наименование практики
		'start',	// Начало практики
		'end',		// Завершение практики
	];

	// Геттеры Laravel
	private static function convert2Date($value): DateTime {
		if ($value instanceof DateTime)
			return $value;
		else {
			$temp = new DateTime($value);
			return $temp;
		}
	}

	protected function start(): Attribute {
		return Attribute::make(
		get: fn($value) => self::convert2Date($value),
		set: fn($value) => self::convert2Date($value),
		);

	}

	protected function end(): Attribute {
		return Attribute::make(
		get: fn($value) => self::convert2Date($value),
		set: fn($value) => self::convert2Date($value),
		);
	}

	public function school() {
		return $this->belongsTo(School::class);
	}

	public function specialties() {
		return $this->hasMany(OrderSpecialty::class, 'orders_specialties');
	}

	public static function createTemplate(): array {
		return [
			'id' => 'order-create',
			'name' => 'order-create',
			'action' => route('orders.store'),
			'close' => route('orders.index'),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'order-edit',
			'name' => 'order-edit',
			'action' => route('orders.update', ['order' => $this->getKey()]),
			'close' => route('orders.index'),
		];
	}
}
