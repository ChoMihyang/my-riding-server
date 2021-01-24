<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'records';
    protected $fillable = ['rec_title'];
//
//    public function select_stats_values()
//    {
//        // 거리, 시간, 평균 속도 값 조회 후 반환
//    }
//
//    public function delete_record()
//    {
//        // 해당 레코드 id 값 조회 후 삭제
//    }
//
//    public function modify_record_name()
//    {
//        // 해당 레코드 id 필드의 rec_title 값 수정
//    }
}

