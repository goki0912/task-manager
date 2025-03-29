<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

class LineController extends Controller
{
    public function redirectToLine(Request $request)
    {
        $token = $request->query('token'); // ← フロントから送られたトークン

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.login_channel_id'),
            'redirect_uri' => config('services.line.redirect_uri'),
            'state' => $token, // ← ここに保存して後で使う
            'scope' => 'profile openid',
        ]);

        return response()->json([
            'url' => "https://access.line.me/oauth2/v2.1/authorize?$query"
        ]);
    }


    public function handleCallback(Request $request)
    {
        $code = $request->get('code');
        $token = $request->get('state'); // ← ここでトークンを受け取る

        // トークンからユーザーを特定
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response('無効なトークン', 401);
        }

        $user = $accessToken->tokenable; // ← トークンに紐づく User モデル
        Auth::login($user); // ← ログインさせる（必要なら）

        // アクセストークン取得
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.line.redirect_uri'),
            'client_id' => config('services.line.login_channel_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        $accessToken = $response['access_token'];

        // LINE プロフィール取得
        $profile = Http::withToken($accessToken)
            ->get('https://api.line.me/v2/profile')
            ->json();

        $user->update(['line_user_id' => $profile['userId'] ?? null]);

        return redirect('http://localhost:5173/tasks');
    }

}
