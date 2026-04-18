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
 * @property int $course_id
 * @property string $name
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property int $capacity
 * @property string $status
 */
class Cohort extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'start_date',
        'end_date',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
