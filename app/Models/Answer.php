<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Answer extends Model implements FormTemplate {
	use HasFactory, HasTitle;

	protected $fillable = [
		'approved', // Одобренное количество практикантов
		'status', // Статус ответа
	];

	public function getTitle(): string {
		return sprintf("%s (%d)",
			$this->orderSpecialty->specialty->getTitle(),
			$this->approved
		);
	}

	public function contract(): BelongsTo {
		return $this->belongsTo(Contract::class);
	}

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}

	public function orderSpecialty(): BelongsTo {
		return $this->belongsTo(OrderSpecialty::class, 'orders_specialties_id');
	}

	public function students(): BelongsToMany {
		return $this->belongsToMany(Student::class, 'answers_students')
			->withTimestamps()
			->withPivot(['status']);
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
