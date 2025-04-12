<?php

namespace App\UseCases\Line;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class HandleLineCallbackUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function execute(string $code, string $token): ?User
    {
        // トークンからユーザーを特定
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            Log::warning("[LINE連携] 無効なトークン: {$token}");
            return null;
        }

        // アクセストークン取得
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.line.redirect_uri'),
            'client_id' => config('services.line.login_channel_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        if (!$response->successful()) {
            Log::error('[LINE連携] アクセストークン取得失敗', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $accessToken = $response['access_token'];

        // LINE プロフィール取得
        $profile = Http::withToken($accessToken)
            ->get('https://api.line.me/v2/profile')
            ->json();

        $lineUserId = $profile['userId'] ?? null;

        if (!$lineUserId) {
            Log::error('[LINE連携] LINEユーザーIDが取得できませんでした。レスポンス: ', $profile);
            return null;
        }

        $this->userRepository->updateLineUserId($user, $lineUserId);

        Log::info("[LINE連携] LINE連携完了: user_id={$user->id}, line_user_id={$lineUserId}");

        return $user;
    }
}
