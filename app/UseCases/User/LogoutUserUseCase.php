<?php
namespace App\UseCases\User;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

readonly class LogoutUserUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(User $user): void
    {
        $this->repository->revokeTokens($user);
    }
}
