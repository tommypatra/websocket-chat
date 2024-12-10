<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangObrolan extends Model
{
    protected $guarded = ['id'];

    public function obrolan()
    {
        return $this->hasMany(Obrolan::class);
    }

    public function userRuang()
    {
        return $this->hasMany(UserRuang::class);
    }
}
