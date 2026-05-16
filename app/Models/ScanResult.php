<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanResult extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'scan_id', 'source', 'raw_data', 'parsed_data', 'risk_score', 'fetched_at',
    ];

    protected $casts = [
        'raw_data'    => 'array',
        'parsed_data' => 'array',
        'fetched_at'  => 'datetime',
    ];

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function sourceLabel(): string
    {
        return match($this->source) {
            'holehe'          => 'Holehe (Sites détectés)',
            'emailrep'        => 'EmailRep.io',
            'breachdirectory' => 'Breach Directory',
            'gravatar'        => 'Gravatar',
            'dns'             => 'DNS / WHOIS',
            'hibp'            => 'HaveIBeenPwned',
            default           => ucfirst($this->source),
        };
    }

    public function sourceIcon(): string
    {
        return match($this->source) {
            'holehe'          => '🔍',
            'emailrep'        => '📊',
            'breachdirectory' => '💥',
            'gravatar'        => '👤',
            'dns'             => '🌐',
            'hibp'            => '🚨',
            default           => '📌',
        };
    }
}
