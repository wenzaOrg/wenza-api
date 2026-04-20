<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ScholarshipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'country',
        'state_or_city',
        'current_status',
        'education_level',
        'course_id',
        'cohort_id',
        'learning_mode',
        'wants_scholarship',
        'prior_tech_experience',
        'wants_job_placement',
        'pipeline_status',
        'admin_notes',
    ];

    protected $casts = [
        'wants_scholarship' => 'boolean',
        'wants_job_placement' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (empty($application->reference_code)) {
                do {
                    $code = 'SCH-'.strtoupper(Str::random(6));
                } while (self::where('reference_code', $code)->exists());
                $application->reference_code = $code;
            }
        });
    }
}
