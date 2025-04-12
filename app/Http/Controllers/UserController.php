<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\UseCases\User\GetMeUseCase;
use App\UseCases\User\GetUserListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(GetUserListUseCase $useCase): JsonResponse
    {
        return ApiResponse::success(null, $useCase->execute());
    }

    public function me(Request $request, GetMeUseCase $useCase): JsonResponse
    {
        $me = $useCase->execute($request->user());

        return ApiResponse::success(null, $me);
    }
}
