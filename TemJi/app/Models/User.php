<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETUGAS = 'petugas';
    public const ROLE_WARGA = 'warga';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
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
     * Periksa apakah pengguna memiliki hak akses Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Periksa apakah pengguna adalah Petugas Lapangan.
     */
    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    /**
     * Periksa apakah pengguna adalah Warga.
     */
    public function isWarga(): bool
    {
        return $this->role === self::ROLE_WARGA;
    }

    /**
     * Dapatkan label peran ramah pengguna dalam Bahasa Indonesia.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Pengelola / Admin',
            self::ROLE_PETUGAS => 'Petugas Lapangan',
            self::ROLE_WARGA => 'Warga Masyarakat',
            default => ucfirst((string) $this->role),
        };
    }

    /**
     * Dapatkan rute pengalihan default berdasarkan peran pengguna setelah login.
     */
    public function getDefaultRedirectRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_PETUGAS => 'petugas.dashboard',
            default => 'warga.dashboard',
        };
    }
}
