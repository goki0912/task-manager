<?php

namespace App\UseCases\User;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class LoginUserUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}
    public function execute(array $credentials): User
    {
        $user = $this->repository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new UnauthorizedHttpException('', 'Invalid credentials.');
        }

        return $user;
    }
}
