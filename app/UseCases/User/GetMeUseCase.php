<?php

namespace App\UseCases\User;

use App\Models\User;

readonly class GetMeUseCase
{
    public function execute(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'line_user_id' => $user->line_user_id,
        ];
    }
}

