<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanShelter extends Model
{
    use HasFactory;

    protected $table = 'permohonan_shelter';

    protected $fillable = [
        'user_id',
        'file',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
