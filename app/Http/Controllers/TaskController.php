<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Task\AssignUsersRequest;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\UseCases\Task\AssignUsersToTaskUseCase;
use App\UseCases\Task\CreateTaskUseCase;
use App\UseCases\Task\DeleteTaskUseCase;
use App\UseCases\Task\GetTaskListUseCase;
use App\UseCases\Task\UpdateTaskUseCase;
use App\UseCases\Task\ShowTaskUseCase;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(GetTaskListUseCase $useCase): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $tasks = $useCase->execute($user);

        return ApiResponse::success(null, $tasks);
    }

    public function store(CreateTaskRequest $request, CreateTaskUseCase $useCase): JsonResponse
    {
        $task = $useCase->execute($request->validated(), auth()->id());

        return ApiResponse::success('タスクを作成しました', $task, 201);
    }

    public function show(Task $task, ShowTaskUseCase $useCase): JsonResponse
    {
        $taskWithUsers = $useCase->execute($task);

        return ApiResponse::success(null, $taskWithUsers);
    }

    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskUseCase $useCase): JsonResponse
    {
        $updated = $useCase->execute($task, $request->validated());

        return ApiResponse::success('タスクを更新しました', $updated);
    }

    public function destroy(Task $task, DeleteTaskUseCase $useCase): JsonResponse
    {
        $useCase->execute($task);

        return ApiResponse::success('タスクを削除しました', null, 200);
    }


    public function assign(AssignUsersRequest $request, Task $task, AssignUsersToTaskUseCase $useCase): JsonResponse
    {
        $userIds = $request->validated()['user_ids'];

        $assignedUsers = $useCase->execute($task, $userIds);

        return ApiResponse::success('アサインしました', [
            'assigned_users' => $assignedUsers
        ]);
    }
    public function toggleDone(Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        $task->is_done = ! $task->is_done;
        $task->save();

        return ApiResponse::success('完了状態を更新しました', $task);
    }
}
