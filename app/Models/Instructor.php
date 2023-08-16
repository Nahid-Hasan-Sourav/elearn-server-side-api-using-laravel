<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
class Instructor extends Model implements JWTSubject
{
    use HasFactory,Notifiable;
    protected $guarded=[];

    protected $hidden = [
        'password',
        // 'remember_token',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
