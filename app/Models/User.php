<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $table = 'users';

    protected $fillable = [
        'user_name', 'user_nickname', 'user_account', 'user_password',
        'user_email', 'user_gender', 'user_birthday', 'user_image',
        'user_phone', 'user_address', 'user_notes', 'user_status',
        'google_uid', 'line_uid', 'line_access_token', 'user_createtime',
        'user_updatetime', 'remember_token'
    ];

    protected $hidden = [
        'user_password', 'remember_token',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthIdentifierName()
    {
        return 'user_email';
    }

    public function getAuthPassword()
    {
        return $this->user_password;
    }
}
