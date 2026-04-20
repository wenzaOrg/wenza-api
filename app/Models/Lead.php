<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $reference_code
 * @property string $pipeline_status
 * @property string $full_name
 * @property string $email
 * @property string|null $phone
 * @property int|null $age
 * @property string|null $employment_status
 * @property string|null $education_level
 * @property string|null $goals
 * @property int|null $course_id
 * @property bool $wants_scholarship
 * @property bool|null $guardian_consent
 * @property string|null $admin_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'pipeline_status',
        'full_name',
        'email',
        'phone',
        'age',
        'employment_status',
        'education_level',
        'goals',
        'course_id',
        'wants_scholarship',
        'guardian_consent',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'wants_scholarship' => 'boolean',
            'guardian_consent' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (! $lead->reference_code) {
                $lead->reference_code = self::generateUniqueReference();
            }
        });
    }

    private static function generateUniqueReference(): string
    {
        do {
            $code = 'LEAD-'.strtoupper(Str::random(6));
        } while (self::where('reference_code', $code)->exists());

        return $code;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
