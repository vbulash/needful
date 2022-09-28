<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

/**
 * @method static findOrFail(mixed $history)
 */
class History extends Model implements FormTemplate
{
    use HasFactory, Notifiable;

	protected $fillable = [
		'timetable_id',
		'status'
	];

	public function timetable(): BelongsTo
	{
		return $this->belongsTo(Timetable::class);
	}

	public function students(): BelongsToMany
	{
		return $this->belongsToMany(Student::class, 'history_student')
			->withPivot('status')
			->withTimestamps();
	}

	public static function createTemplate(): array
	{
		return [];	// Создать запись истории стажировок через CRUD нельзя
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'history-edit',
			'name' => 'history-edit',
			'action' => route('history.update', ['history' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('history.index', ['sid' => session()->getId()]),
		];
	}
}
