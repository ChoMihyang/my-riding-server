<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notiCheck(Notification $id)
    {
        /**
         *  해당 알림 확인 검사
         *  Param : 해당 알림 레코드 id 값
         *  Request : X
         *  Response
         *  <<-- 알림 확인 성공 시 -->>
         *       [
         *           "message" => "알림 확인이 완료되었습니다."
         *           "data"    => (Notification) noti_check 필드 값, updated_at 필드 값
         */
    }

    public function notiPageMove(Notification $id)
    {
        /**
         *  알림 클릭 시 페이지 이동
         *  Param : 해당 알림 레코드 id 값
         *  Request : 회원 정보 토큰
         *  Response
         *      <<-- 요청 성공 시 -->>
         *       [
         *           "message" => "페이지 이동이 완료되었습니다."
         *           "data"    => (Notification)noti_url 필드 값
         *        ], 200
         *      <<-- 요청 실패 시 -->>
         *      [
         *           "message" => "페이지 이동이 완료되지 않았습니다."
         *           "data"    => null
         *       ], 403
         */
    }
}
