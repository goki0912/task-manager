<?php
namespace App\UseCases\Auth;

use App\Models\User;
use App\Repositories\AuthRepositoryInterface;

readonly class LogoutUserUseCase
{
    public function __construct(private AuthRepositoryInterface $repository) {}

    public function execute(User $user): void
    {
        $this->repository->revokeTokens($user);
    }
}
