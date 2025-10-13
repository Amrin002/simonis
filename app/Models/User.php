<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Get guru yang terhubung dengan user ini
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    /**
     * Get orang tua yang terhubung dengan user ini
     */
    public function orangTua()
    {
        return $this->hasOne(OrangTua::class, 'user_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is guru
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user is orang tua
     */
    public function isOrangTua(): bool
    {
        return $this->role === 'orangtua';
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'guru' => 'Guru',
            'orangtua' => 'Orang Tua',
            default => 'Unknown',
        };
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'danger',
            'guru' => 'primary',
            'orangtua' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get nama lengkap (dari guru atau orang tua)
     */
    public function getNamaLengkapAttribute(): string
    {
        if ($this->isGuru() && $this->guru) {
            return $this->guru->nama;
        }

        if ($this->isOrangTua() && $this->orangTua) {
            return $this->orangTua->nama;
        }

        return $this->username;
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk admin
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope untuk guru
     */
    public function scopeGuru($query)
    {
        return $query->where('role', 'guru');
    }

    /**
     * Scope untuk orang tua
     */
    public function scopeOrangTua($query)
    {
        return $query->where('role', 'orangtua');
    }
}
