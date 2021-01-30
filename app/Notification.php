<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    public function getDashboardNoti(int $user_id)
    {
        $user_noti = Notification::select([
            'noti_type as type',
            'noti_msg as msg',
            'created_at'
        ])
            ->where('noti_user_id', $user_id)
            ->where('noti_check', 0)
            ->get();

        return $user_noti;
    }
}
