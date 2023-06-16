<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Договор на практику
 *
 * @property string $number
 * @property \DateTime $sealed
 * @property string $title
 * @property \DateTime $start
 * @property \DateTime $finish
 * @property string $scan
 * @property \App\Models\School $school
 * @property \App\Models\Employer $employer
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
			$this->number, $this->sealed->format('d.m.Y'));
	}

	public function school(): BelongsTo {
		return $this->belongsTo(School::class);
	}

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}

	public function answers(): HasMany {
		return $this->hasMany(Answer::class);
	}

	public static function uploadScan(Request $request, string $scan = null) {
		if ($request->hasFile('scan')) {
			if ($scan)
				Storage::delete($scan);
			$folder = date('Y-m-d');
			return $request->file('scan')->store("scans/{$folder}");
		}
		return null;
	}
}
