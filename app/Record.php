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



    public function delete_record()
    {
        // 해당 레코드 id 값 조회 후 삭제
    }

    public function modify_record_name()
    {
        // 해당 레코드 id 필드의 rec_title 값 수정
    }
}

