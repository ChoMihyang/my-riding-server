<?php

namespace App;

use Carbon\Traits\Date;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Record extends Model
{
    protected $table = 'records';
    protected $fillable = ['rec_title'];

    /**
     *  특정 연도 내 하나의 주차 통계 조회
     * @param int $user_id
     * @param int $year
     * @param int $week
     * @return Collection
     */
    public function select_stats_by_week(
        int $user_id,
        int $year,
        int $week
    ): Collection
    {
        dd('dd');
        $param = [
            'created_at as date',
            'rec_route_id as id',
            'rec_distance as distance',
            'rec_time as time',
            'rec_avg_speed as avg_speed',
            'rec_score as score',
            'rec_title as title'
        ];

//        $record_stats_by_week = Record::select($param)
//            ->

    }

    public function delete_record()
    {
        // 해당 레코드 id 값 조회 후 삭제
    }

    public function modify_record_name()
    {
        // 해당 레코드 id 필드의 rec_title 값 수정
    }
}

