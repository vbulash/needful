<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model implements FormTemplate
{
    use HasFactory;

	protected $fillable = [
		'approved',	// Одобренное количество практикантов
	];

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}

	public function orderSpecialty(): BelongsTo {
		return $this->belongsTo(OrderSpecialty::class, 'orders_specialties');
	}

	public static function createTemplate(): array {
		return [
			'id' => 'answer-create',
			'name' => 'answer-create',
			'action' => '',
			'close' => '',
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'answer-edit',
			'name' => 'answer-edit',
			'action' => route('employers.orders.answers.update', ['answer' => $this->getKey()]),
			'close' => '',
		];
	}
}
