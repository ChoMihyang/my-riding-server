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
        'rec_user_id', 'rec_route_id', 'rec_title', 'rec_time', 'rec_distance',
        'rec_score', 'rec_start_point_address', 'rec_end_point_address',
        'rec_avg_speed', 'rec_max_speed', 'created_at'
    ];

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
            ->join('stats', 'records.created_at', 'stats.stat_date')
            ->where('stats.stat_date', $ridingDate)
            ->where('rec_user_id', $user_id)
            ->get();

        return $resultData;
    }

    /**
     * 웹 라이딩 일지 주차별 기록 조회
     * @param int $user_id
     * @param string $start_date
     * @param string $end_date
     */
    public function getRecordsByWeek(
        int $user_id,
        string $start_date,
        string $end_date
    )
    {
        $param = [
            'id',
            'created_at as date',
            'rec_title as title',
            'rec_distance as distance',
            'rec_time as time',
            'rec_score as score'
        ];
        $returnData = Record::select($param)
            ->where('rec_user_id', $user_id)
            ->wherebetween('created_at', [$start_date, $end_date])
            ->get();

        return $returnData;
    }

    /**
     * @param int $user_id
     * @param int $record_id
     * @return Collection
     */
    public function getRecordOfDay(
        int $user_id,
        int $record_id
    ): Collection
    {
        $param = [
            'id',
            'rec_title as title',
            'created_at as date',
            'rec_start_point_address as startAddress',
            'rec_end_point_address as endAddress',
            'rec_distance as distance',
            'rec_time as time',
            'rec_avg_speed as avgSpeed',
            'rec_max_speed as maxSpeed'
        ];

        $returnData = Record::select($param)
            ->where('rec_user_id', $user_id)
            ->where('id', $record_id)
            ->get();

        return $returnData;
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
        ?int $rec_route_id,
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
            'rec_user_id' => $rec_user_id,
            'rec_route_id' => $rec_route_id,
            'rec_title' => $rec_title,
            'rec_distance' => $rec_distance,
            'rec_time' => $rec_time,
            'rec_score' => $rec_score,
            'rec_start_point_address' => $rec_start_point_address,
            'rec_end_point_address' => $rec_end_point_address,
            'rec_avg_speed' => $rec_avg_speed,
            'rec_max_speed' => $rec_max_speed,
            'created_at' => now(),
        ]);
    }

    // 경로 전체 순위
    public function rankSort(
        int $route_id // 경로 아이디
    )
    {
        // 순위 정렬 해야함
        $param = [
            'id',
            'rec_user_id',
            'rec_score',
            'created_at',
            'rec_max_speed',
            'rec_time'
        ];

        return Record::select($param)
                ->where('rec_route_id',$route_id)
                ->orderBy('rec_time')
                ->get();
    }

    // 내 라이딩 기록
    public function myRecord(
        int $rec_route_id, // 경로 아이디
        int $rec_user_id
    )
    {
        // 전제 카운트 가져오고 내순위 나타내야됨..

        // 이 경로의 전체 카운트 -> record 대신에, route 에서 num_of_try_count 사용?
        $allRankCount = $this->rankSort($rec_route_id)->count(); // 횟수 유저 중복 ok

        // 이 경로의 나의 카운트 -> rec_user_id, rec_route_id 체크하고, 내 기록중 첫번째 값 반환
        $myRank = self::where('rec_route_id', $rec_route_id)
            ->where('rec_user_id', $rec_user_id)
            ->orderBy('rec_time')
            ->first(); // controller 에서 first 해주는 걸로 바꾸자.. 내 기록 평균 내는것 떄문에..



//        $kk = self::select(DB::raw('
//          SELECT s.*, @rank := @rank + 1 rank FROM (
//            SELECT rec_user_id, rec_score FROM t
//            GROUP BY rec_user_id
//          ) s, (SELECT @rank := 0) init
//          ORDER BY rec_score DESC
//        ')
//        );



//        $kk = self::select('rec_user_id', DB::raw('rec_score'))
////        ->groupBy('rec_user_id')
//        ->get();
//        dd($kk);

//        // 나의 등수?
//        self::where('rec_route_id', $rec_route_id)
//            ->where('rec_user_id', $rec_user_id);
//        self::addSelect([]);
//
//        // 이 경로의 가장 빠른 기록 -> 시간
//        $first_score = $this->rankSort($rec_route_id)->first();
//        dd($first_score);

        // 평균 기록

    }
}

