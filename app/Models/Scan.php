<?php

namespace App\Models;

use App\Enums\ScanStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Scan extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'uuid', 'email_target', 'contact_email', 'status',
        'ip_address', 'started_at', 'completed_at', 'error_message',
        'from_cache', 'access_token',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Scan $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->access_token)) {
                $model->access_token = Str::random(40);
            }
        });
    }

    protected $casts = [
        'status'       => ScanStatus::class,
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'from_cache'   => 'boolean',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(ScanResult::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [ScanStatus::Completed, ScanStatus::Cached]);
    }

    public function isFailed(): bool
    {
        return $this->status === ScanStatus::Failed;
    }

    public function duration(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at->diffInSeconds($this->started_at);
        }
        return null;
    }

    public function maskedEmail(): string
    {
        $parts = explode('@', $this->email_target);
        if (count($parts) !== 2) {
            return $this->email_target;
        }

        $user   = $parts[0];
        $domain = $parts[1];

        if (strlen($user) <= 2) {
            return substr($user, 0, 1) . '***@' . $domain;
        }

        return substr($user, 0, 1) . '***' . substr($user, -1) . '@' . $domain;
    }

    /**
     * Validate that the provided token matches this scan's access_token.
     * Uses timing-safe comparison to prevent timing attacks.
     */
    public function validateToken(?string $token): bool
    {
        if (empty($this->access_token) || empty($token)) {
            return false;
        }

        return hash_equals($this->access_token, $token);
    }
}
