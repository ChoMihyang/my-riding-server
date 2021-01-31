<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\This;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
     * 대시보드 사용자 정보(UserController - dashboard)
     * @param int $user_id
     * @return Collection
     */
    public function getDashboardUserInfo(
        int $user_id
    ): Collection
    {
        $param = [
            'user_nickname as nickname',
            'user_score_of_riding as score',
            'user_num_of_riding as count',
            'date_of_latest_riding as last_riding',
            'user_picture as picture'
        ];

        $user_info = self::select($param)
            ->where('id', $user_id)
            ->get();

        return $user_info;
    }
}
