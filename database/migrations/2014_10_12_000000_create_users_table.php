<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * users Table
     *
     * id - 사용자 레코드 번호
     * user_account - 사용자 아이디
     * user_password - 사용자 비밀번호
     * user_nickname - 사용자 닉네임
     * user_picture - 사용자 프로필 사진
     * user_num_of_riding - 총 주행 횟수
     * user_score_of_riding - 사용자 점수
     * date_of_latest_riding - 최근 주행한 날짜
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_account');
            $table->string('user_password');
            $table->string('user_nickname');
            $table->string('user_picture');
            $table->unsignedBigInteger('user_num_of_riding');
            $table->unsignedBigInteger('user_score_of_riding');
            $table->dateTime('date_of_latest_riding');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
