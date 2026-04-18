<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $avatar_url
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }
}
