<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'order',
        'content_type',
        'content_url',
        'content_body',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
