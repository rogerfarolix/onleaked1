<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EmailCache extends Model
{
    use HasUuids;

    protected $table = 'email_caches';

    protected $fillable = [
        'email_hash', 'email_domain', 'breaches_count',
        'accounts_count', 'risk_level', 'cached_data', 'expires_at',
    ];

    protected $casts = [
        'cached_data' => 'array',
        'expires_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function findByEmail(string $email): ?self
    {
        $hash = hash('sha256', strtolower(trim($email)));
        return static::where('email_hash', $hash)
                     ->where('expires_at', '>', now())
                     ->first();
    }

    public static function storeForEmail(string $email, array $data, int $ttlHours = 24): self
    {
        $hash = hash('sha256', strtolower(trim($email)));
        $domain = substr(strrchr($email, '@'), 1);

        return static::updateOrCreate(
            ['email_hash' => $hash],
            [
                'email_domain'    => $domain,
                'breaches_count'  => $data['breaches_found'] ?? 0,
                'accounts_count'  => $data['accounts_found'] ?? 0,
                'risk_level'      => $data['risk_level'] ?? 'A',
                'cached_data'     => $data,
                'expires_at'      => now()->addHours($ttlHours),
            ]
        );
    }
}
