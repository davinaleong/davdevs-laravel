<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'totp_enabled', 'totp_secret', 'totp_confirmed_at', 'recovery_codes'];

    protected $hidden = ['password', 'remember_token', 'totp_secret', 'recovery_codes'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'totp_confirmed_at' => 'datetime',
            'totp_enabled' => 'boolean',
            'recovery_codes' => 'array',
            'password' => 'hashed',
        ];
    }

    public function has2faSetup(): bool
    {
        return $this->totp_secret && $this->totp_enabled;
    }

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (self::query()->exists()) {
                throw new \RuntimeException('Only one user account may exist at a time. Delete the existing account before creating a new one.');
            }
        });
    }
}
