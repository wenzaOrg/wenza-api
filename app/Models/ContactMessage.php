<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $reference_code
 * @property string $full_name
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property bool $is_read
 * @property Carbon|null $replied_at
 * @property string|null $admin_notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'full_name',
        'email',
        'subject',
        'message',
        'is_read',
        'replied_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'replied_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ContactMessage $message) {
            if (! $message->reference_code) {
                $message->reference_code = self::generateUniqueReference();
            }
        });
    }

    private static function generateUniqueReference(): string
    {
        do {
            $code = 'MSG-'.strtoupper(Str::random(6));
        } while (self::where('reference_code', $code)->exists());

        return $code;
    }
}
