<?php

namespace App\UseCases\User;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

readonly class RegisterUserUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(array $data): User
    {
        return $this->repository->createUser($data);
    }
}
