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
    protected $fillable = [
        'rec_user_id','rec_route_id','rec_title','rec_time','rec_distance',
        'rec_score','rec_start_point_address','rec_end_point_address',
        'rec_avg_speed','rec_max_speed','created_at'
    ];

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
//        dd('dd');
//        $param = [
//            'created_at as date',
//            'rec_route_id as id',
//            'rec_distance as distance',
//            'rec_time as time',
//            'rec_avg_speed as avg_speed',
//            'rec_score as score',
//            'rec_title as title'
//        ];

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

    // User  <-> Record 모델 다대다 관계 선언
    public function user()
    {
        // User  <-> Record 모델 다대다 관계 선언
        return $this->belongsToMany(User::class);
    }

    /**
     * 기록 생성
     *
     * @param int $rec_user_id
     * @param int $rec_route_id
     * @param string $rec_title
     * @param float $rec_distance
     * @param int $rec_time
     * @param int $rec_score
     * @param string $rec_start_point_address
     * @param string $rec_end_point_address
     * @param float $rec_avg_speed
     * @param float $rec_max_speed
     */
    public function createRecord(
        int $rec_user_id,
        int $rec_route_id,
        string $rec_title,
        float $rec_distance,
        int $rec_time,
        int $rec_score,
        string $rec_start_point_address,
        string $rec_end_point_address,
        float $rec_avg_speed,
        float $rec_max_speed
    )
    {
        self::create([
            'rec_user_id'=>$rec_user_id,
            'rec_route_id'=>$rec_route_id,
            'rec_title'=>$rec_title,
            'rec_distance'=>$rec_distance,
            'rec_time'=>$rec_time,
            'rec_score'=>$rec_score,
            'rec_start_point_address'=>$rec_start_point_address,
            'rec_end_point_address'=>$rec_end_point_address,
            'rec_avg_speed'=>$rec_avg_speed,
            'rec_max_speed'=>$rec_max_speed,
            'created_at'=>now(),
        ]);
    }
}

