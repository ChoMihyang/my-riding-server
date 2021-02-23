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

    /**
     * 알림 확인 시 알림 테이블 업데이트
     *
     * @param int $user_id
     * @param Notification $noti_record_id
     */
    public function checkNotification(
        int $user_id,
        Notification $noti_record_id
    )
    {
        $record_id = $noti_record_id['id'];


        $notification = Notification::where('noti_user_id', $user_id)
            ->find($record_id);

        $notification->noti_check = false;
        $notification->updated_at = date('Y-m-d');
        $notification->save();
    }
}
