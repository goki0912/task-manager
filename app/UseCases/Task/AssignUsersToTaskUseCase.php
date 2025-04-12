<?php
namespace App\UseCases\Task;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

readonly class AssignUsersToTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(Task $task, array $userIds): Collection
    {
        Gate::authorize('update', $task);

        $this->taskRepository->assignUsers($task, $userIds);

        return $task->assignedUsers()->get();
    }
}
