<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'totp_enabled'];

    protected $hidden = ['password', 'remember_token', 'totp_secret', 'recovery_codes'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'totp_confirmed_at' => 'datetime',
            'totp_enabled'      => 'boolean',
            'recovery_codes'    => 'array',
            'password'          => 'hashed',
        ];
    }

    public function has2faSetup(): bool
    {
        return $this->totp_secret && $this->totp_enabled;
    }
}
