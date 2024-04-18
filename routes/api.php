<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:sanctum'])->group(function () {
    // 이 그룹에 속하는 모든 라우트는 'auth:sanctum' 미들웨어를 적용합니다.

    // 로그아웃
    Route::post('/logout', [AuthController::class, 'logout']);

    // 질문 등록
    Route::post('/questions', [QuestionController::class, 'store']);

    // 답변 선택
    Route::put('questions/{questionId}/answers/{answerId}/select', [QuestionController::class, 'selectAnswer']);

    // 답변 작성
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store']);

    // 답변 삭제
    Route::delete('/answers/{answer}', [AnswerController::class, 'delete']);
});

// 비인증 사용자를 위한 라우트
Route::get('/questions', [QuestionController::class, 'index']); // 질문 리스트 가져오기 - 비회원 조회 가능
Route::get('/questions/{question}', [QuestionController::class, 'show']); // 질문과 답변 가져오기 - 비회원 조회 가능

// 인증 관련 라우트
Route::post('/register', [AuthController::class, 'register']); // 회원가입
Route::post('/login', [AuthController::class, 'login']); // 로그인

// OAuth 관련 라우트
Route::get('login/github', [AuthController::class, 'redirectToGithub']); // GitHub 로그인
Route::get('callback/github', [AuthController::class, 'handleGithubCallback']); // GitHub 콜백


