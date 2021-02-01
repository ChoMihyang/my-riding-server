<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Collection\Collection;

class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * 대시보드 알림 정보 (UserController - dashboard)
     * @param int $user_id
     * @return mixed
     */
    public function getDashboardNoti(
        int $user_id
    )
    {
        $param = [
            'noti_type as type',
            'noti_msg as msg',
            'noti_url as url',
            'created_at'
        ];

        $user_noti = Notification::select($param)
            ->where('noti_user_id', $user_id)
            ->where('noti_check', 0)
            ->orderByDesc('created_at')
            ->get();

        return $user_noti;
    }
}
