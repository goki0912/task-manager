<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\UseCases\User\LoginUserUseCase;
use App\UseCases\User\LogoutUserUseCase;
use App\UseCases\User\RegisterUserUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserUseCase $useCase): JsonResponse
    {
        $user = $useCase->execute($request->validated());
        $token = $user->generateAuthToken();

        return ApiResponse::success('ユーザー登録に成功しました', [
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request, LoginUserUseCase $useCase): JsonResponse
    {
        $user = $useCase->execute($request->validated());
        $token = $user->generateAuthToken();

        return ApiResponse::success('ログインに成功しました', [
            'token' => $token,
        ]);
    }

    public function logout(Request $request, LogoutUserUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->user());

        return ApiResponse::success('ログアウトしました', null);
    }
}
