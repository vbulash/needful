<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int federal
 * @property int order
 * @property string code
 * @property string $name
 * @property string degree
 * @method static findOrFail(mixed $specialty_id)
 */
class Specialty extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'federal',	// Признак федерального справочника профессий
		'order',	// Номер по порядку в федеральном справочнике
		'code',		// Код из федерального справочника
		'name',		// Название специальности
		'degree',	// Квалификация по федеральному справочнику
	];

	public function level0(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'level0_id');
	}

	public function level1(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'level1_id');
	}

	public function level2(): BelongsTo
	{
		return $this->belongsTo(Item::class, 'level2_id');
	}

	public function learns(): HasMany
	{
		return $this->hasMany(Learn::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'specialty-create',
			'name' => 'specialty-create',
			'action' => route('specialties.store', ['sid' => session()->getId()]),
			'close' => route('specialties.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'specialty-edit',
			'name' => 'specialty-edit',
			'action' => route('specialties.update', ['specialty' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('specialties.index', ['sid' => session()->getId()]),
		];
	}

	public function fspecialties(): HasMany
	{
		return $this->hasMany(Fspecialty::class);
	}

	public function getTitle(): string
	{
		return $this->name;
	}
}
