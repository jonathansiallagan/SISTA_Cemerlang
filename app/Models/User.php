<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'username', 'password', 'role', 'cabang_id', 'avatar', 'preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'preferences' => 'array',
        ];
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /** Apakah user adalah Admin Pusat / Admin Cabang */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['Admin Pusat', 'Admin Cabang'], true);
    }

    public function isCustomerService(): bool
    {
        return $this->role === 'Customer Service';
    }

    /** Nama route dashboard sesuai role user */
    public function homeRoute(): string
    {
        if ($this->role === 'Customer Service') {
            return 'cs.dashboard';
        }
        
        return 'admin.dashboard';
    }
}