<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static findOrFail(int $id)
 */
class Teacher extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',		// ФИО преподавателя
		'position',	// Должность преподавателя
	];

	public function job(): MorphTo
	{
		return $this->morphTo();
	}

	public function learns(): BelongsToMany
	{
		return $this->belongsToMany(Learn::class, 'teacher_learn')
			->withTimestamps();
	}

	public function getTitle(): string
	{
		return $this->name;
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'teacher-create',
			'name' => 'teacher-create',
			'action' => route('teachers.store', ['sid' => session()->getId()]),
			'close' => route('teachers.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'teacher-edit',
			'name' => 'teacher-edit',
			'action' => route('teachers.update', ['teacher' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('teachers.index', ['sid' => session()->getId()]),
		];
	}
}
