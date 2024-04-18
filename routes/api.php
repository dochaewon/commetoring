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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login/github', [AuthController::class, 'redirectToGithub']);
Route::get('callback/github', [AuthController::class, 'handleGithubCallback']);

// 회원가입
Route::post('/register', [AuthController::class, 'register']);

// 로그인
Route::post('/login', [AuthController::class, 'login']);

// 로그아웃
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::get('/questions', [QuestionController::class, 'index']); //질문 리스트 가져오기 - 비회원 조회 가능
Route::get('/questions/{question}', [QuestionController::class, 'show']); //질문과 답변 가져오기 - 비회원 조회 가능
Route::middleware('auth:sanctum')->post('/questions', [QuestionController::class, 'store']); // 질문 등록 - 비회원 접속 불가능
Route::middleware('auth:sanctum')->put('questions/{questionId}/answers/{answerId}/select', [QuestionController::class, 'selectAnswer']);

Route::middleware('auth:sanctum')->post('/questions/{question}/answers', [AnswerController::class, 'store']); //답변 작성 - 비회원 접속 불가능
Route::middleware('auth:sanctum')->delete('/answers/{answer}', [AnswerController::class, 'delete']); //답변 삭제

