<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obrolan extends Model
{
    protected $guarded = ['id'];

    public function ruangObrolan()
    {
        return $this->belongsTo(RuangObrolan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
