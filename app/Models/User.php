<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FormTemplate
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

	public function student(): HasOne
	{
		return $this->hasOne(Student::class);
	}

	public function employer(): HasOne
	{
		return $this->hasOne(Employer::class);
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
}
