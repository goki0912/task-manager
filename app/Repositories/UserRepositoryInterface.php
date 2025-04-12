<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getAllUsers(): Collection;
    public function findById(int $id): User;
    public function createUser(array $data): User;
    public function findByEmail(string $email): ?User;
    public function revokeTokens(User $user): void;
    public function findByToken(string $token): ?User;
    public function updateLineUserId(User $user, string $lineUserId): void;
}

