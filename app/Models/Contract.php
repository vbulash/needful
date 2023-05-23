<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Договор на практику
 *
 * @property string $number
 * @property \DateTime $sealed
 * @property string $title
 * @property \DateTime $start
 * @property \DateTime $finish
 * @property string $scan
 */
class Contract extends Model {
	use HasTitle, GrantedAll;

	protected $fillable = [
		'number', // Номер договора
		'sealed', // Дата подписания договора
		'title', // Название контракта практики
		'start', // Дата начала практики
		'finish', // Дата завершения практики
		'scan', // Скан бумажного договора
	];

	protected $casts = [
		'sealed' => 'datetime',
		'start' => 'datetime',
		'finish' => 'datetime',
	];

	public function getTitle(): string {
		return sprintf("%s от %s",
			$this->number, $this->start->format('Y.m.d'));
	}

	public function school(): BelongsTo {
		return $this->belongsTo(School::class);
	}

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}

}
