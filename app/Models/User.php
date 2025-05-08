<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Like;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'no_telp',
        'jenis_kelamin',
        'tgl_lahir',
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
        'tgl_lahir' => 'date',  // Pastikan tanggal lahir di-cast sebagai date
    ];

    //user bisa memiliki banyak hewan
    public function hewan()
    {
        return $this->hasMany(Hewan::class, 'user_id');
    }

    //user bisa mendapatkan banyak notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    //user bisa mengajukan banyak permohonan adopsi
    public function permohonanAdopsi()
    {
        return $this->hasMany(PermohonanAdopsi::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
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
