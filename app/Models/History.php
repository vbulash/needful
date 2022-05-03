<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class History extends Model implements FormTemplate
{
    use HasFactory, Notifiable;

	protected $fillable = [
		'timetable_id',
		'student_id',
		'status'
	];

	public function timetable() {
		return $this->belongsTo(Timetable::class);
	}

	public function student() {
		return $this->belongsTo(Student::class);
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
