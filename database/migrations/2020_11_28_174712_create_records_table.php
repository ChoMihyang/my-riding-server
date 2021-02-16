<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * record Table
     *
     * id - 기록 레코드 번호
     * rec_user_id - 현 기록을 보유한 사용자 id
     * rec_route_id - 현 기록이 저장된 경로 id
     * rec_title - 사용자가 주행 후 저장한 기록 이름
     * rec_distance - 주행 후 기록된 거리
     * rec_time - 주행 후 기록된 시간
     * rec_score - 주행 후 계산된 점수
     * rec_start_point_address - 주행 출발점
     * rec_end_point_address - 주행 도착점
     * rec_avg_speed - 주행 후 기록된 평균 속도
     * rec_max_speed - 주행 후 기록된 최고 속도
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rec_user_id');
            $table->foreign('rec_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('rec_route_id')->nullable();
            $table->foreign('rec_route_id')->references('id')->on('routes');
            $table->string('rec_title');
            $table->unsignedDouble('rec_distance');
            $table->unsignedBigInteger('rec_time');
            $table->unsignedBigInteger('rec_score');
            $table->string('rec_start_point_address');
            $table->string('rec_end_point_address');
            $table->unsignedDouble('rec_avg_speed');
            $table->unsignedDouble('rec_max_speed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}
