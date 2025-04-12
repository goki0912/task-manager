<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;


class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function getTasksByUser(User $user): Collection
    {
        return $user->tasks()
            ->with('assignedUsers:id,name')
            ->latest()
            ->get();
    }
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function loadRelations(Task $task, array $relations): Task
    {
        return $task->load($relations);
    }
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }
    public function delete(Task $task): void
    {
        $task->delete();
    }
    public function assignUsers(Task $task, array $userIds): void
    {
        $task->assignedUsers()->sync($userIds);
    }
}
