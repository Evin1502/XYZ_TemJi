<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN   = 'admin';
    public const ROLE_PETUGAS = 'petugas';
    public const ROLE_WARGA   = 'warga';

    protected $fillable = ['name', 'email', 'password', 'role', 'wilayah_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi
    public function warga(): HasOne
    {
        return $this->hasOne(Warga::class, 'user_id');
    }

    /** Wilayah tugas: hanya petugas / admin wilayah (PBI 19-20). */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function pengajuanDaurUlang(): HasMany
    {
        return $this->hasMany(PengajuanDaurUlang::class);
    }

    public function penjemputanTambahan(): HasMany
    {
        return $this->hasMany(PenjemputanTambahan::class);
    }

    // Scope (scopeWarga milik Daffa diganti nama agar tidak bentrok dengan relasi warga())
    public function scopePetugas(Builder $q): Builder   { return $q->where('role', self::ROLE_PETUGAS); }
    public function scopeRoleWarga(Builder $q): Builder { return $q->where('role', self::ROLE_WARGA); }

    // Helper peran
    public function isAdmin(): bool   { return $this->role === self::ROLE_ADMIN; }
    public function isPetugas(): bool { return $this->role === self::ROLE_PETUGAS; }
    public function isWarga(): bool   { return $this->role === self::ROLE_WARGA; }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN   => 'Pengelola / Admin',
            self::ROLE_PETUGAS => 'Petugas Lapangan',
            self::ROLE_WARGA   => 'Warga Masyarakat',
            default            => ucfirst((string) $this->role),
        };
    }

    /** Satu sumber kebenaran wilayah: warga -> warga.wilayah_id, selain itu users.wilayah_id. */
    public function getWilayahIdEfektifAttribute(): ?int
    {
        return $this->isWarga() ? $this->warga?->wilayah_id : $this->wilayah_id;
    }

    public function getDefaultRedirectRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN   => 'admin.dashboard',
            self::ROLE_PETUGAS => 'petugas.dashboard',
            default            => 'warga.dashboard',
        };
    }
}
