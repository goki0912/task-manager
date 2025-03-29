<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * ログインユーザーのタスク一覧
     */
    public function index(): JsonResponse
    {
        $tasks = Auth::user()
            ->tasks()
            ->with('assignedUsers:id,name') // 👈 アサインユーザーの名前だけを取得
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    /**
     * タスク作成
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'remind_before_minutes' => 'nullable|integer|min:1|max:1440', // 最大24時間
        ]);

        $task = Auth::user()->tasks()->create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ], 201);
    }

    /**
     * タスク詳細（認可ポリシー適用）
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load('assignedUsers'); // ←追加

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ]);
    }

    /**
     * タスク更新（認可ポリシー適用）
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ]);
    }

    /**
     * タスク削除（認可ポリシー適用）
     */
    public function destroy(Task $task): Response
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task); // タスク作成者のみがアサイン可能

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // アサイン（同期させたいなら sync()、追加だけなら attach()）
        $task->assignedUsers()->sync($validated['user_ids']);

        return response()->json([
            'status' => 'success',
            'message' => 'Users assigned to task successfully',
            'assigned_users' => $task->assignedUsers()->get(),
        ]);
    }
}
