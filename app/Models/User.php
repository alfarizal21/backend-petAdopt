<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'name',
        'email',
        'password',
        'role',
        'no_telp',
        'alamat',
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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // Untuk Laravel 10+, memastikan password aman.
        'role' => 'string',       // Pastikan role di-cast sebagai string
    ];

    /**
     * Relasi antara User dan Hewan
     * Satu user (shelter) bisa memiliki banyak hewan yang tersedia untuk diadopsi
     */
    public function hewan()
    {
        return $this->hasMany(Hewan::class, 'id_user');
    }

    /**
     * Relasi antara User dan Notifikasi
     * User (adopter atau shelter) bisa menerima banyak notifikasi
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user');
    }

    /**
     * Relasi antara User dan PermohonanAdopsi
     * Seorang user (adopter) bisa membuat banyak permohonan adopsi
     */
    public function permohonanAdopsi()
    {
        return $this->hasMany(PermohonanAdopsi::class, 'id_user');
    }

    /**
     * Relasi antara User dan Adopsi
     * User (adopter) bisa memiliki banyak riwayat adopsi
     */
    // public function riwayatAdopsi()
    // {
    //     return $this->hasMany(RiwayatAdopsi::class, 'id_user');
    // }
}
