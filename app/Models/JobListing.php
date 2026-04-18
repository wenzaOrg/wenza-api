<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'title',
        'description',
        'location',
        'type',
        'apply_url',
        'posted_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
