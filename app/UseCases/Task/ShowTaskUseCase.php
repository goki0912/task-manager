<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;


readonly class ShowTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(Task $task): Task
    {
        Gate::authorize('view', $task);

        return $this->taskRepository->loadRelations($task, ['assignedUsers']);
    }
}

