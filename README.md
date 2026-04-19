# metheyou-manager

믿어유(metheyou) 서비스 통합 관리 웹앱

## 1. 프로젝트 개요
- **프로젝트명**: metheyou-manager
- **목적**: '믿어유' 서비스의 전반적인 운영, 계정 관리 및 데이터 분석을 위한 관리자용 통합 시스템
- **아키텍처**: 자체 구축한 경량 MVC(Model-View-Controller) 패턴 적용

## 2. 주요 기술 스택
- **Backend**: PHP 8.x
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Package Manager**: Composer
- **Environment**: `.env` 기반 설정 (`vlucas/phpdotenv` 사용)

## 3. 주요 기능
- **인증 관리 (Auth)**: 관리자 로그인 및 세션 제어
- **계정 관리 (Account)**: 서비스 이용자 및 관리자 계정 조회, 관리
- **분석 통계 (Analysis)**: 서비스 주요 지표 및 데이터 분석 대시보드 제공

## 4. 핵심 디렉토리 구조
```text
metheyou-manager/
├── public/         # 웹 서버 Document Root (CSS, JS, 이미지 등 정적 리소스)
├── src/            # 애플리케이션 코어 로직
│   ├── Controllers/# 요청 처리 및 비즈니스 로직 제어
│   ├── Models/     # 데이터베이스 통신 및 데이터 구조화
│   ├── Views/      # 화면 렌더링 (UI 레이아웃 및 페이지)
│   └── Core/       # 라우터 처리, 데이터베이스 커넥션 등 핵심 모듈
├── vendor/         # Composer 의존성 패키지
├── composer.json   # 패키지 및 오토로드(PSR-4) 설정
├── index.php       # 애플리케이션 엔트리 포인트
└── .env            # 환경 변수 설정 (데이터베이스 접속 정보 등)
```

## 5. 설치 및 구동 방법

**1. 패키지 설치**
프로젝트 루트에서 Composer를 이용해 의존성 패키지 설치
```sh
composer install
```

**2. 환경 변수 설정**
루트 디렉토리에 `.env` 파일을 생성하고 필요한 설정 값(DB 연결 정보 등) 입력

**3. 웹 서버 설정**
- Nginx 또는 Apache 등의 웹 서버 Document Root를 프로젝트의 `public/` 디렉토리로 설정
- (개발 환경) PHP 내장 서버를 사용할 경우 아래 명령어를 실행하세요:
```sh
php -S localhost:8000 -t public
```
