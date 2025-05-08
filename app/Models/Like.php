<?php

// app/Models/Like.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'hewan_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hewan()
    {
        return $this->belongsTo(Hewan::class);
    }
}

