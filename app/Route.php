<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';

    public function routeValue()
    {
        // routeLike 테이블 조인
        // 경로명, 예상 시간, 고도, 시도 횟수, 좋아요 수 등 정보 조회
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
