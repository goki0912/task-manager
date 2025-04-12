<?php

namespace App\UseCases\User;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Collection;

readonly class GetUserListUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(): Collection
    {
        return $this->repository->getAllUsers();
    }
}
