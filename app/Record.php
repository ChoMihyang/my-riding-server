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
     * @param int $year
     * @param int $user_id
     *
     * @return Collection
     */

    // 해당 연도, 주차, 시작일, 마지막일, 요일, 거리, 시간, 평균 속도
    public function select_stats_values(int $year, int $user_id)
    {



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

