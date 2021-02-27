# my-riding-server

## 2021-01-31(일)
#### 1. 회원가입, 로그인, 로그아웃, 프로필 정보 기능 추가
- dash board 완성 후 ApiAuthController.php , api.php 수정해야함


## 2021-01-24(일)

#### 1. 모의 DB를 위한 Table Schema 생성
- Migration Class
    - CreateUsersTable 
        - 사용자 아이디, 비밀번호, 닉네임, 사진 정보
        - 총 주행 횟수, 점수, 최근 주행 날짜 정보
    
    - CreateStatsTable 
        - 현 통계의 주행 날짜, 연도, 주차, 요일 정보
        - 주행 거리, 시간, 평균 속도 정보
        - 현 사용자의 주행 통계 수 정보
        
    - CreateRoutesTable 
        - 현 경로를 생성한 사용자, 경로 이름, 거리, 시간, 출발점, 도착점 정보
        - 현 경로의 좋아요 수, 주행 누적 수, 사용자 수 정보
        - 현 경로의 평균 경사도, 최고 고도, 최저 고도 정보
        
    - CreateRouteLikesTable 
        - 현 경로에 좋아요를 누른 사용자 정보
        - 좋아요를 받은 경로 정보
        
    - CreateRecordTable
        - 현 기록을 보유한 사용자, 점수 정보
        - 현 기록이 저장된 경로 정보
        - 기록 이름, 거리, 시간, 출발점, 도착점, 평균 속도, 최고 속도 정보
        
    - CreateNotificationsTable
        - 현 알림을 받은 사용자 정보
        - 알림 유형, 메시지, 이동 페이지 주소, 확인 유무 정보
     
    - CreateBadgesTable
        - 배지 보유자의 통계 정보
        - 배지 유형, 배지 이름 정보
    
    - CreateIpNumbersTable
        - ip 사용자 정보
        - 전방 휴대폰 ip, 후방 휴대폰 ip, 포트 정보
        
#### 2. Mock Data 삽입
- Seeds Class
    - UserTableSeeder
    - StatsTableSeeder
    - RouteTableSeeder
    - RouteLikeTableSeeder
    - RecordTableSeeder
    - NotificationTableSeeder
    - BadgeTableSeeder
    - IpNumberTableSeeder
    
    
## 2021-01-31(일)
### 대시보드 페이지 출력 (UserController@dashboard)
1. 사용자 프로필(User.php)
    - getDashboardUserInfo(사용자 번호) 
    - Return : 사진, 닉네임, 점수, 최근 라이딩, 라이딩 총 횟수 
    
2. 통계 요약(Stats.php)
    - getDashboardStats(사용자 번호, 연도, 주차)
    - Return : 요일, 거리, 시간, 평균 속도
    
3. 읽지 않은 알림(Notification.php) 
    - getDashboardNoti(사용자 번호)
    - Return : 알림 유형, 메시지, 주소, 알림 발생일
    
    
## 2021-02-04(목)
1. Notification 시딩 파일 수정
    - 알림 클릭 시 이동 경로
    
## 2021-02-05(금)
1. 라이딩 일지 - 조회 페이지 구현

## 2021-02-08(월)
1. 라이딩 일지 (연도별) 페이지 구현
2. 라이딩 일지 (주차별) 페이지 구현 진행중

## 2021-02-09(화)
1. 라이딩 일지 (주차별) 페이지 구현 진행중
2. 앱 홈화면 join 쿼러 보충 후 마무리 예정
    
## 2021-02-11(목)
1. 앱 홈 화면 구현
2. 앱 랭킹 화면 구현

## 2021-02-13(토)
1. 웹 라이딩 일지 주차별 리스트 진행중

## 2021-02-17(수)
1. 웹 페이지 완료(부가 기능 제외)
2. 앱 홈 화면 기록 조회 완료
3. 앱 랭킹 화면 조회 완료

## 2021-02-20(토)
1. 앱 랭킹 사용자 상세 보기 완료

## 2021-02-22(월)
1. 웹 대시보드 최근 라이딩 기록 구현 
2. 올해 누적 통계 구현

## 2021-02-23(화)
1. 웹 알림 확인 구현

## 2021-02-25(목)
1. 앱 배지 구현 진행중
2. 웹 라이딩 기록 제목 수정 진행중
3. 웹 라이딩 기록 삭제 진행중

## 2021-02-26 (금)
1. 앱 배지 구현 진행중

## 2021-02-27 (토)
1. 웹 대시보드 버그 수정
2. 웹 기록 제목 수정 구현
 
