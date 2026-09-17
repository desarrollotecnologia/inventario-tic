<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_COORDINADOR = 'coordinador';
    const ROLE_ANALISTA = 'analista';
    const ROLE_APOYO = 'apoyo';

    const ROLES = [
        self::ROLE_COORDINADOR => 'Coordinador TIC',
        self::ROLE_ANALISTA => 'Analista TIC',
        self::ROLE_APOYO => 'Apoyo TIC',
    ];

    public function isCoordinador(): bool
    {
        return $this->role === self::ROLE_COORDINADOR;
    }

    public function isAnalista(): bool
    {
        return $this->role === self::ROLE_ANALISTA;
    }

    public function isApoyo(): bool
    {
        return $this->role === self::ROLE_APOYO;
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, [self::ROLE_COORDINADOR, self::ROLE_ANALISTA]);
    }

    public function canManageActivities(): bool
    {
        return true; // Todos los roles pueden ver y registrar
    }

    public function canDeleteActivities(): bool
    {
        return $this->role === self::ROLE_COORDINADOR;
    }

    public function canEditActivities(): bool
    {
        return in_array($this->role, [self::ROLE_COORDINADOR, self::ROLE_ANALISTA]);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
