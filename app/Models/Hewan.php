<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hewan extends Model
{
    use HasFactory;

    protected $table = 'hewan';

    protected $fillable = [
        'user_id',
        'nama',
        'jenis',
        'usia',
        'status',
        'deskripsi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permohonanAdopsi()
    {
        return $this->hasMany(PermohonanAdopsi::class, 'hewan_id');
    }
}
