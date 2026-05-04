<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Wajib untuk JWT

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     * Kita tambahkan 'role' agar bisa membedakan Admin dan Member.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
    ];

    /**
     * Atribut yang harus disembunyikan dari JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut untuk keamanan password.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Implementasi JWT: Mengambil ID User untuk disimpan di token.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Implementasi JWT: Menyisipkan data tambahan (role) ke dalam token.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role // Role tersimpan aman di dalam token
        ];
    }
}