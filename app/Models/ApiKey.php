<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'name', 'key', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
