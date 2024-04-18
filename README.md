# 코멘토링 API

고양이 온라인 익명 멘토링 서비스입니다.

## 요구 사항

- PHP >= 8.0.2
- Laravel 9.19
- Laravel Sanctum을 사용한 API 인증
- Laravel Socialite를 사용한 GitHub OAuth 로그인
- API 문서를 위한 Darkaonline/l5-swagger
- HTTP 요청을 위한 GuzzleHttp

## 설치

1. 레포지토리를 클론합니다:

    ```bash
    git clone https://github.com/dochaewon/commetoring.git
    ```

2. 프로젝트 디렉터리로 이동합니다:

    ```bash
    cd 프로젝트-디렉터리
    ```

3. Composer 종속성을 설치합니다:

    ```bash
    composer install
    ```

4. `.env` 파일을 설정합니다. 데이터베이스 자격 증명 및 기타 설정을 입력합니다:

    ```bash
    cp .env.example .env
    ```

5. 애플리케이션 키를 생성합니다:

    ```bash
    php artisan key:generate
    ```

6. 데이터베이스를 마이그레이션합니다:

    ```bash
    php artisan migrate
    ```

7. (선택 사항) 샘플 데이터로 데이터베이스를 시드합니다:

    ```bash
    php artisan db:seed
    ```

8. Laravel 개발 서버를 시작합니다:

    ```bash
    php artisan serve
    ```

## 사용법

### 엔드포인트

- **POST /api/register**: 새로운 사용자를 등록합니다.
- **POST /api/login**: 이메일과 비밀번호로 로그인합니다.
- **POST /api/logout**: 현재 인증된 사용자를 로그아웃합니다.
- **GET /api/login/github**: GitHub OAuth 로그인으로 리디렉션합니다.
- **GET /api/callback/github**: GitHub OAuth 콜백을 처리합니다.
- **POST /api/questions/{questionId}/answers**: 질문에 대한 새로운 답변을 생성합니다.
- **DELETE /api/answers/{answerId}**: 특정 답변을 삭제합니다.
- **PUT /api/questions/{questionId}/answers/{answerId}/select**: 질문에 대한 답변을 선택합니다.

### API 문서

애플리케이션을 실행한 후 `/api/documentation/index.html`에서 API 문서를 확인할 수 있습니다. 각 엔드포인트, 요청 매개변수 및 응답에 대한 자세한 정보를 제공합니다.

## 기여

기여는 환영입니다! 문제점을 발견하거나 개선 제안이 있는 경우 이슈를 열거나 풀 리퀘스트를 제출해주세요.

## 라이선스

이 프로젝트는 MIT 라이선스에 따라 공개 소프트웨어로 사용이 허가됩니다. [MIT 라이선스](LICENSE)를 참조하세요.
