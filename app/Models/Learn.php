<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property mixed $start
 * @property mixed $finish
 * @property string $new_school
 * @property string $new_specialty
 * @property int $status
 * @method static create(array $data)
 * @method static findOrFail(int $id)
 */
class Learn extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'start',			// Дата поступления
		'finish',			// Дата завершения
		'new_school',		// Новое учебное заведение
		'new_specialty',	// Новая специальность
		'status',			// Статус активности объекта
	];

	public function getTitle(): string
	{
		$name = $this->school->short ?? $this->new_school;
		$start = $this->start->format('d.m.Y');
		$finish = isset($this->finish) ? $this->finish->format('d.m.Y') : 'н/вр';
		return sprintf("%s (с %s по %s)", $name, $start, $finish);
	}

	// Геттеры Laravel

	/**
	 * @throws Exception
	 */
	private static function convert2Date($value): ?DateTime
	{
		if($value instanceof DateTime)
			return $value;
		elseif (isset($value))
			return new DateTime($value);
		else return null;
	}

	protected function start(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);

	}

	protected function finish(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);

	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function school(): BelongsTo
	{
		return $this->belongsTo(School::class);
	}

	public function specialty(): BelongsTo
	{
		return $this->belongsTo(Specialty::class);
	}

	public function teachers(): BelongsToMany
	{
		return $this->belongsToMany(Teacher::class, 'teacher_learn')
			->withTimestamps();
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'learn-create',
			'name' => 'learn-create',
			'action' => route('learns.store', ['sid' => session()->getId()]),
			'close' => route('learns.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'learn-edit',
			'name' => 'learn-edit',
			'action' => route('learns.update', ['learn' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('learns.index', ['sid' => session()->getId()]),
		];
	}


}
