<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

#[Fillable(['name', 'email', 'password', 'role', 'alert_frequency', 'suspended_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at'      => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isSuspended(): bool
    {
        return !is_null($this->suspended_at);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    public function scanHistory()
    {
        return $this->hasMany(ScanHistory::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }
}
