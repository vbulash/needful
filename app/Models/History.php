<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @method static findOrFail(mixed $history)
 */
class History extends Model implements FormTemplate
{
    use HasFactory, Notifiable, HasTitle, GrantedAll;

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
			->using(Trainee::class)
			->withPivot(['id', 'status'])
			->withTimestamps();
	}

	public function teacher(): BelongsTo {
		return $this->belongsTo(Teacher::class);
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

	public function getTitle(): string
	{
		return sprintf("%s - %s (%s)",
			$this->timetable->internship->employer->getTitle(),
			$this->timetable->internship->getTitle(),
			Str::lower($this->timetable->getTitle())
		);
	}
}
