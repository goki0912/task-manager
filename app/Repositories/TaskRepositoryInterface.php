<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;


interface TaskRepositoryInterface
{
    public function getTasksByUser(User $user): Collection;
    public function create(array $data): Task;
    public function loadRelations(Task $task, array $relations): Task;
    public function update(Task $task, array $data): Task;
    public function delete(Task $task): void;
    public function assignUsers(Task $task, array $userIds): void;
}
