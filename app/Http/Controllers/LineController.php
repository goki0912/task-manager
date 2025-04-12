<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\UseCases\Line\GenerateLineRedirectUrlUseCase;
use App\UseCases\Line\HandleLineCallbackUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LineController extends Controller
{
    public function redirectToLine(Request $request, GenerateLineRedirectUrlUseCase $useCase): JsonResponse
    {
        $url = $useCase->execute($request->query('token'));

        return ApiResponse::success(null, ['url' => $url]);
    }

    public function handleCallback(Request $request, HandleLineCallbackUseCase $useCase)
    {
        $user = $useCase->execute(
            $request->get('code'),
            $request->get('state')
        );

        if (!$user) {
            return redirect(config('app.frontend_url') . '/line/error');
        }

        Auth::login($user);

        return redirect(config('app.frontend_url') . '/tasks');
    }
}
