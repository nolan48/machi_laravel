<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Log;

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
        Log::info('Getting JWT Identifier: ' . $this->getKey());
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

    // 定义 User 和 Article 之间的一对多关系
    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id_fk');
    }

    // 定义 User 和 ArticleComment 之间的一对多关系
    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'user_id_fk');
    }
}
