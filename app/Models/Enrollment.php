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
class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cohort_id',
        'enrolled_at',
        'payment_status',
        'scholarship_applied',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
