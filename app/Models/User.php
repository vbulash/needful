<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static create(array $array)
 * @method static findOrFail(int $id)
 * @method static find($getKey)
 */
class User extends Authenticatable implements FormTemplate
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Notifiable, Right;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
		'special'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public function students(): HasMany
	{
		return $this->hasMany(Student::class);
	}

	public function employers(): HasMany
	{
		return $this->hasMany(Employer::class);
	}

	public function tasksFrom(): HasMany
	{
		return $this->hasMany(Task::class, 'from_id');
	}

	public function tasksTo(): HasMany
	{
		return $this->hasMany(Task::class, 'to_id');
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'user-create',
			'name' => 'user-create',
			'action' => route('users.store', ['sid' => session()->getId()]),
			'close' => route('users.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'user-edit',
			'name' => 'user-edit',
			'action' => route('users.update', ['user' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('users.index', ['sid' => session()->getId()]),
		];
	}

	public static function special()
	{
		return User::where('special', true)->first();
	}

	/**
	 * Get all the models from the database.
	 *
	 * @param  array|string  $columns
	 * @return Collection<int, static>
	 */
	public static function all($columns = ['*']): Collection
	{
		$special = User::special()->getKey();
		return parent::all()->except($special);
	}

	public function allowed(string|object $element): MorphToMany
	{
		$class = is_object($element) ? get_class($element) : $element;
		return $this
			->morphedByMany($class, 'right', 'rights')
			->withTimestamps();
	}
}
