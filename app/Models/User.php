<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function obrolan()
    {
        return $this->hasMany(Obrolan::class);
    }

    public function userRuang()
    {
        return $this->hasMany(UserRuang::class);
    }


    public function kawanUser1()
    {
        return $this->hasMany(Kawan::class, 'user_id1', 'id');
    }

    public function kawanUser2()
    {
        return $this->hasMany(Kawan::class, 'user_id2', 'id');
    }
}
