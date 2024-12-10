<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupObrolan extends Model
{
    protected $guarded = ['id'];

    public function grup()
    {
        return $this->belongsTo(Grup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
