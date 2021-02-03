<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';

    public function routeValue()
    {
        // routeLike 테이블 조인 -> 좋아요 숫자 가져오기
        // 경로명, 예상 시간, 고도, 시도 횟수, 좋아요 수 등 정보 조회
        // 등록일         : route_create_at
        // 경로명         : route_title
        // 거리            : route_distance
        // 예상 시간     : route_time
        // 좋아요 수     : route_like
        // 시도횟수      : route_num_of_try_count
        // 출발지         : route_start_point_address
        // 도착지         : route_end_point_address
        // 지도 이미지  : route_image

        $userID = 1;
        $routeInfo = self::where('route_user_id',$userID);
//        return $routeInfo;
//        $routeTitle = $routeInfo->route_title;

        dd($routeInfo);

//        return $routeInfo;
    }

    public function routeDelete()
    {
        // 해당 레코드 id 값 조회 후 삭제
    }

    public function routeSave()
    {
        // 경로 정보 전달 받은 후
        // 새 경로 레코드 생성
    }
}
