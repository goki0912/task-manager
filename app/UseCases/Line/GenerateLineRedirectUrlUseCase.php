<?php
namespace App\UseCases\Line;

class GenerateLineRedirectUrlUseCase
{
    public function execute(string $token): string
    {
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.login_channel_id'),
            'redirect_uri' => config('services.line.redirect_uri'),
            'state' => $token,
            'scope' => 'profile openid',
        ]);

        return "https://access.line.me/oauth2/v2.1/authorize?$query";
    }
}
