<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 全ユーザーの一覧を返す（自分も含める）
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'line_user_id' => $user->line_user_id,
        ]);
    }
}
