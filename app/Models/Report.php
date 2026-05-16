<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasUuids;

    protected $fillable = [
        'scan_id', 'accounts_found', 'breaches_found',
        'risk_level', 'gravatar_url', 'full_report',
        'email_sent', 'email_sent_at',
    ];

    protected $casts = [
        'risk_level'    => RiskLevel::class,
        'full_report'   => 'array',
        'email_sent'    => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function riskColor(): string
    {
        return $this->risk_level?->color() ?? '#6b7280';
    }

    public function riskLabel(): string
    {
        return $this->risk_level?->label() ?? 'Inconnu';
    }
}
