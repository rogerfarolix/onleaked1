<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanHistory extends Model
{
    public $timestamps = false;

    protected $table = 'scan_history';

    protected $fillable = ['user_id', 'scan_type', 'target', 'results'];

    protected $casts = [
        'results'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
