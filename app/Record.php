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
     * 해당 날짜의 통계 반환 (RecordController - recordOfHome)
     * @param int $user_id
     * @param int $year
     * @param int $month
     * @param int $day
     * @return Collection
     */
    public function select_records_of_day(
        int $user_id,
        int $year,
        int $month,
        int $day
    ): Collection
    {
        $ridingDate = $year . '-' . $month . '-' . $day;
        $ridingDate = '1997-01-17';

        $param = [
            'stats.stat_date as date',
            'rec_title as title',
            'rec_time as time',
            'rec_distance as distance',
            'rec_avg_speed as avg_speed',
            'rec_max_speed as max_speed',
            'rec_start_point_address as start_point',
            'rec_end_point_address as end_point'
        ];

        $resultData = Record::select($param)
            ->join('stats', 'records.created_at', 'stats.id')
            ->where('rec_user_id', $user_id)
            ->where('stats.stat_date', $ridingDate)
            ->get();

        return $resultData;
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

