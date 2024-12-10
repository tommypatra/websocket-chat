<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kawan extends Model
{
    protected $guarded = ['id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id1', 'id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id2', 'id');
    }
}
