<?php

namespace App\UseCases\Task;

use App\Models\User;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Collection;

readonly class GetTaskListUseCase
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(User $user): Collection
    {
        return $this->taskRepository->getTasksByUser($user);
    }
}
