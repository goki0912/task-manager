<?php

use App\Models\Task;
use App\Models\User;

/**
 * @see \App\Http\Controllers\TaskController::store
 */
it('認証ユーザーがタスクを作成できる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $data = [
        'title' => 'Test Task',
        'description' => 'This is a test',
        'due_date' => now()->addDay()->toDateTimeString(),
    ];

    // Act
    $response = $this->withToken($token)->postJson('/api/tasks', $data);

    // Assert
    $response->assertCreated();
    $response->assertJsonFragment(['title' => 'Test Task']);
});

/**
 * @see \App\Http\Controllers\TaskController::index
 */
it('認証ユーザーが自分のタスク一覧を取得できる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    Task::factory()->count(3)->for($user)->create();

    // Act
    $response = $this->withToken($token)->getJson('/api/tasks');

    // Assert
    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

/**
 * @see \App\Http\Controllers\TaskController::show
 */
it('認証ユーザーが自分のタスク詳細を取得できる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $task = Task::factory()->for($user)->create();

    // Act
    $response = $this->withToken($token)->getJson("/api/tasks/{$task->id}");

    // Assert
    $response->assertOk();
    $response->assertJsonFragment(['id' => $task->id]);
});

/**
 * @see \App\Http\Controllers\TaskController::update
 */
it('認証ユーザーが自分のタスクを更新できる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $task = Task::factory()->for($user)->create();

    // Act
    $response = $this->withToken($token)->putJson("/api/tasks/{$task->id}", [
        'title' => 'Updated Title',
        'description' => 'Updated desc',
        'due_date' => now()->addDays(2)->toDateTimeString(),
    ]);

    // Assert
    $response->assertOk();
    $response->assertJsonFragment(['title' => 'Updated Title']);
});

/**
 * @see \App\Http\Controllers\TaskController::destroy
 */
it('認証ユーザーが自分のタスクを削除できる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $task = Task::factory()->for($user)->create();

    // Act
    $response = $this->withToken($token)->deleteJson("/api/tasks/{$task->id}");

    // Assert
    $response->assertJsonFragment(['message' => 'タスクを削除しました']);
    expect(Task::find($task->id))->toBeNull();
});

/**
 * @see \App\Http\Controllers\TaskController::toggleDone()
 */
it('自分のタスクの完了状態を切り替えられる', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $task = Task::factory()->for($user)->create(['is_done' => false]);

    // Act
    $response = $this->withToken($token)->patchJson("/api/tasks/{$task->id}/toggle");

    // Assert
    $response->assertOk();
    $response->assertJsonPath('status', 'success');
    $response->assertJsonPath('data.is_done', true);
    expect($task->fresh()->is_done)->toBeTrue();
});

/**
 * @see \App\Http\Controllers\TaskController::toggleDone()
 */
it('他人のタスクの完了状態は変更できない', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $task = Task::factory()->for($otherUser)->create(['is_done' => false]);

    // Act
    $response = $this->withToken($token)->patchJson("/api/tasks/{$task->id}/toggle");

    // Assert
    $response->assertForbidden();
    expect($task->fresh()->is_done)->toBeFalse();
});
