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
            'id',
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

    // 주행 완료 시 알림 생성
    public function insertNotification(
        int $user_id,
        int $type
    )
    {
        $riding_year = date('Y');
        $riding_month = date('m');
        $riding_date = date('d');

        $noti_msg = '';
        // 주행 완료 알림 메시지
        $riding_noti_msg = $riding_year . '년 ' . $riding_month . '월 ' . $riding_date . '일 라이딩이 완료되었습니다.';
        // 배지 획득 알림 메시지
        $badge_noti_msg = '새로운 기록을 갱신하였습니다.';

        if ($type == 1002) {
            $noti_msg = $riding_noti_msg;
        } else if ($type == 1003) {
            $noti_msg = $badge_noti_msg;
        }

        Notification::insert([
            'noti_user_id' => $user_id,
            'noti_type' => $type,
            'noti_msg' => $noti_msg,
            'noti_url' => '없음',
            'noti_check' => false,
            'created_at' => now()
        ]);
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

        $notification->noti_check = true;
        $notification->updated_at = date('Y-m-d');
        $notification->save();
    }
}
