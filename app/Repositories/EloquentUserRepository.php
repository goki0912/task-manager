<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getAllUsers(): Collection
    {
        return User::select('id', 'name')->get();
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function revokeTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * LINEコールバックで state=token を元にユーザーを取得
     */
    public function findByToken(string $token): ?User
    {
        return PersonalAccessToken::findToken($token)?->tokenable;
    }

    /**
     * LINEの userId を保存
     */
    public function updateLineUserId(User $user, string $lineUserId): void
    {
        $user->update(['line_user_id' => $lineUserId]);
    }
}
