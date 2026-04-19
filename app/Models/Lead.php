<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $reference
 * @property string $full_name
 * @property string $email
 * @property string|null $phone
 * @property int|null $course_id
 * @property string $referral_source
 * @property string|null $motivation
 * @property string $status
 */
class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'full_name',
        'email',
        'phone',
        'course_id',
        'referral_source',
        'motivation',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
