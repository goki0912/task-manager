<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証API（ユーザー登録・ログイン・ログアウト）
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 認証が必要なAPI（トークンがないとアクセス不可）
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/me', [UserController::class, 'me']);

    Route::get('/tasks', [TaskController::class, 'index']);          // 一覧
    Route::post('/tasks', [TaskController::class, 'store']);         // 作成
    Route::get('/tasks/{task}', [TaskController::class, 'show']);    // 詳細
    Route::put('/tasks/{task}', [TaskController::class, 'update']);  // 更新
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); // 削除

    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign'])->middleware('auth:sanctum');

    Route::get('/line/login', [LineController::class, 'redirectToLine']);
});
    Route::get('/line/callback', [LineController::class, 'handleCallback']);
