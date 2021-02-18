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
            ->where('rec_user_id', $user_id)
            ->where('stats.stat_date', $ridingDate)
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
<<<<<<< HEAD
        // users 테이블과 join
        return Record::join('users','users.id','=','records.rec_user_id')
            ->select('users.id',
                     'users.user_account',
                     'records.rec_user_id',
                     'records.rec_route_id',
                     'records.rec_score',
                     'records.rec_max_speed',
                     'records.rec_time',
                     'records.rec_title',
                     'records.created_at')
            ->where('rec_route_id',$route_id)
            ->orderBy('rec_time')
            ->get();
=======
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

>>>>>>> ea7b0537143f7abbb74d15fb9c07d82db3398393
    }

    // 내 라이딩 기록
    public function myRecord(
        int $rec_route_id, // 경로 아이디
        int $rec_user_id
    )
    {
<<<<<<< HEAD
        // 선택한 경로의 기록 전체 카운트
        $allRankCount = $this->rankSort($rec_route_id)->count();

        // 선택한 경로의 나의 모든 기록
        $userRecord = self::where('rec_route_id', $rec_route_id)
=======
        // 전제 카운트 가져오고 내순위 나타내야됨..

        // 이 경로의 전체 카운트 -> record 대신에, route 에서 num_of_try_count 사용?

        $allRankCount = $this->rankSort($rec_route_id)->count(); // 횟수 유저 중복 ok


        // 이 경로의 나의 카운트 -> rec_user_id, rec_route_id 체크하고, 내 기록중 첫번째 값 반환
        $myRank = self::where('rec_route_id', $rec_route_id)
>>>>>>> ea7b0537143f7abbb74d15fb9c07d82db3398393
            ->where('rec_user_id', $rec_user_id)
            ->orderBy('rec_time')
            ->get();

        // 내 기록중 첫번째 값 반환
        $myRecordFirst = $userRecord->first();
        // 내 최고 기록 시간
        $myTopRecord = $myRecordFirst->getAttribute('rec_time'); // 반환할 값

        // 내 기록의 count
        $userRecordCount = $userRecord->count();
        // 선택 경로, 나의 모든 기록 시간
        $userAllRecords = array_column($userRecord->toArray(), 'rec_time');
        // 나의 모든 기록 총 합계
        $userRecordSum = array_sum($userAllRecords);
        // 나의 모든 기록 평균
        $userRecordAvg = ($userRecordSum/$userRecordCount); // 반환할 값

        // 이 경로의 가장 빠른 기록의 사용자
        $first_score = $this->rankSort($rec_route_id)->first();
        $first_score_user_id = $first_score->rec_user_id;  // 반환할 값 아이디
        $first_score_time    = $first_score->rec_time;     // 반환할 값 기록
        $first_score_account = $first_score->user_account; // 반환할 값 계정

        // 순위 카운트 출력
        $ranks = $this->rankSort($rec_route_id);

        // collection 배열로 변환
        $rankArrays = $ranks->toArray();

        $rankValue = array();
        // 등수 배열 초기화
        for ($j = 0; $j < $allRankCount; $j++) {
            $rankValue[$j] = 1;
        }
        // 등수 반영, 동점자 포함
        for ($i = 0; $i < $allRankCount; $i++) {
            $rankValue[$i] = 1;

            for ($k = 0; $k < $allRankCount; $k++) {
                if ($rankArrays[$i]["rec_score"] < $rankArrays[$k]["rec_score"]) {
                    $rankValue[$i]++;
                }
            }
        }

        // 결과 값들 종합한 배열 생성
        $resultValue = array();
        for ($m = 0; $m < $allRankCount; $m++) {
            $resultValue = [
                'rec_rank' => $rankValue[$m],
                'user_account' => $rankArrays[$m]["user_account"],
                'rec_user_id' => $rankArrays[$m]["rec_user_id"],
                'rec_route_id' => $rankArrays[$m]["rec_route_id"],
                'rec_time' => $rankArrays[$m]["rec_time"],
                'rec_score' => $rankArrays[$m]["rec_score"]
            ];
            if ($rankArrays[$m]["rec_user_id"] == $rec_user_id)
                break;
        }

        // 유저의 랭킹
        $userRankValue = $resultValue["rec_rank"];

        return $queryValue = [
            'record_user_rank'=>$userRankValue,
            'record_user_account'=>$resultValue["user_account"],
            'record_all_count'=>$allRankCount,
            'record_user_top'=>$myTopRecord,
            'record_user_avg'=>$userRecordAvg,
            'record_top_score_user_id'=>$first_score_user_id,
            'record_top_score_user_account'=>$first_score_account,
            'record_top_score_user_time'=>$first_score_time
        ];
    }
}

