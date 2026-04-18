<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $title
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property int $duration_weeks
 * @property string $format
 * @property string|null $thumbnail_url
 * @property bool $is_published
 * @property int $price_ngn
 * @property float|null $price_usd
 * @property int|null $scholarship_price_ngn
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'category',
        'description',
        'duration_weeks',
        'format',
        'price_ngn',
        'price_usd',
        'scholarship_price_ngn',
        'thumbnail_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course): void {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function scholarshipApplications(): HasMany
    {
        return $this->hasMany(ScholarshipApplication::class);
    }
}
