<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',		// ФИО преподавателя
		'position',	// Должность преподавателя
	];

	public function job()
	{
		return $this->morphTo();
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
