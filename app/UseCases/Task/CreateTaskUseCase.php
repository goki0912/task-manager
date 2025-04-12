<?php

namespace App\UseCases\Task;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;

readonly class CreateTaskUseCase
{
    public function __construct(private TaskRepositoryInterface $repository) {}

    public function execute(array $data, int $userId): Task
    {
        return $this->repository->create(array_merge($data, ['user_id' => $userId]));
    }
}
