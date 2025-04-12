<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\Repositories\AuthRepositoryInterface;

readonly class RegisterUserUseCase
{
    public function __construct(private AuthRepositoryInterface $repository) {}

    public function execute(array $data): User
    {
        // リポジトリに登録を依頼して、ユーザーを返す
        return $this->repository->createUser($data);
    }
}
