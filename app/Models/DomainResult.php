<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DomainResult extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'share_uuid',
        'domain',
        'results',
        'expires_at',
    ];

    protected $casts = [
        'results'    => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->share_uuid ??= Str::uuid()->toString();
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
