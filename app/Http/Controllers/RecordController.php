<?php

namespace App\Http\Controllers;

use App\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function recordViewByYear()
    {
        /**
         * 연도별 일지 및 통계 조회
         *  Param : X
         *  Request : 회원 정보 토큰, 스크롤 페이지 번호, 연도
         *  Response
         *     <<-- 조회 성공 시 -->>
         *      [
         *         "message" => "조회에 성공하였습니다."
         *         "data"    => [
         *              "recodeData" : (Record)주 시작-마지막 일, 주차별 총 거리, 총 시간, 평균 속도
         *              "statsData" : (Stats)요일별 거리, 시간, 평균 속도
         *           ]
         *       ], 200
         *     <<-- 조회 실패 시 -->>
         *      [
         *          "message" => "조회에 실패하였습니다."
         *          "data"    => null
         *       ], 403
         */
    }

    public function recordViewByWeek()
    {
        /**
         * 주차별 일지 및 통계 조회
         *  Param : X
         *  Request : 회원 정보 토큰, 레코드 id 값
         *  Response
         *      <<-- 조회 성공 시 -->>
         *       [
         *           "message" => "조회에 성공하였습니다."
         *           "data"    => [
         *                 "recordData" : (Record) 주차별 총 거리, 시간, 평균 속도, 점수
         *                  / 해당 주차 내 날짜, 일지 제목, 거리, 시간, 점수
         *                 "statsData" : (Stats) 요일별 거리, 시간, 평균 속도
         *            ]
         *        ], 200
         *      <<-- 조회 실패 시 -->>
         *       [
         *           "message" => "조회에 실패하였습니다."
         *           "data" => null
         *        ], 403
         */
    }

    public function recordDetailView(Record $id)
    {
        /**
         *  일별 일지 상세 조회
         * Param : 해당 날짜 레코드의 id값
         * Request : 회원 정보 토큰
         * Response
         *     <<-- 조회 성공 시 -->>
         *      [
         *        "message" => "조회에 성공하였습니다."
         *        "data"    => [
         *                "recordData" : (Record) 일지 제목, 날짜, 출발지, 도착지,
         *                               거리, 평균 속도, 최고 속도, 소요 시간
         *         ]
         *      ], 200
         *      <<-- 조회 실패 시 -->>
         *      [
         *         "message" => "조회에 실패하였습니다."
         *         "data"    => null
         *       ], 403
         */
    }

    public function recordDelete(Record $id)
    {
        /**
         *  라이딩 일지 삭제
         *  Param : 해당 날짜 레코드의 id값
         *  Request : 회원 정보 토큰
         *  Response
         *      <<-- 삭제 성공 시 -->>
         *      [
         *         "message" => "삭제가 완료되었습니다."
         *         "data" => null
         *       ], 200
         *       <<-- 삭제 실패 시 -->>
         *       [
         *         "message" => "삭제가 완료되지 않았습니다."
         *         "data" => null
         *       ], 403
         */

    }

    public function recordModify(Record $id)
    {
        /**
         *  일지 제목 수정
         *  Param : 해당 레코드의 id값
         *  Request : 회원 정보 토큰, 변경할 값(rec_title)
         *  Response
         *      <<-- 수정 성공 시 -->>
         *       [
         *         "message" => "수정이 완료되었습니다."
         *         "data" => [ (Record)일지 제목 ]
         *        ], 200
         *       <<-- 수정 실패 시 -->>
         *       [
         *          "message" => "수정이 완료되지 않았습니다."
         *          "data"    => null
         *        ], 403
         */
    }
}


