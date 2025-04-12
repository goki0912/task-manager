<?php
namespace App\UseCases\Task;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;


readonly class UpdateTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(Task $task, array $data): Task
    {
        Gate::authorize('update', $task);

        return $this->taskRepository->update($task, $data);
    }
}
