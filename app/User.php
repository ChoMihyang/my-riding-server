<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $fillable = [
        'user_account', 'user_password',
        'user_nickname', 'user_picture',
        'user_num_of_riding', 'user_score_of_riding',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 유저 생성
     *
     * @param string $user_account
     * @param string $user_password
     * @param string $user_nickname
     * @param string $user_picture
     * @return mixed
     */
    public function createUserInfo(
        string $user_account,
        string $user_password,
        string $user_nickname,
        string $user_picture
    )
    {
        return self::create([
            'user_account'  => $user_account,
            'user_password' => $user_password,
            'user_nickname' => $user_nickname,
            'user_picture'  => $user_picture
        ]);
    }

    /**
     * 사용자 목록 전부 가지고 오기
     *
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function indexUserList()
    {
        return self::all();
    }
}
