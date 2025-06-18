<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanAdopsi extends Model
{
    use HasFactory;

    protected $table = 'permohonan_adopsi';

    protected $fillable = [
        'user_id',
        'hewan_id',
        'nama',
        'umur',
        'no_hp',
        'email',
        'nik',
        'jenis_kelamin',
        'tempat_tanggal_lahir',
        'pekerjaan',
        'alamat',
        'riwayat_adopsi',
        'status',
        'tanggal_permohonan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hewan()
    {
        return $this->belongsTo(Hewan::class, 'hewan_id', 'id');
    }
}
