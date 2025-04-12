<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;

readonly class DeleteTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(Task $task): void
    {
        Gate::authorize('update', $task);

        $this->taskRepository->delete($task);
    }
}
