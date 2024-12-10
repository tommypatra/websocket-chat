<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{

    protected $guarded = ['id'];

    public function grupObrolan()
    {
        return $this->hasMany(GrupObrolan::class);
    }

    public function grupUser()
    {
        return $this->hasMany(GrupUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
