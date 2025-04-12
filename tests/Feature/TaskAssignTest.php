<?php

use App\Models\User;
use App\Models\Task;

/**
 * @see \App\Http\Controllers\TaskController::assign
 */
test('作成者は他のユーザーをタスクにアサインできる', function () {
    // Arrange
    $creator = User::factory()->create();
    $assignees = User::factory()->count(2)->create();
    $token = $creator->createToken('test')->plainTextToken;

    $task = Task::factory()->for($creator)->create();

    // Act
    $response = $this->withToken($token)->postJson("/api/tasks/{$task->id}/assign", [
        'user_ids' => $assignees->pluck('id')->toArray(),
    ]);

    // Assert
    $response->assertOk();
    $response->assertJsonFragment(['message' => 'アサインしました']);
    expect($task->assignedUsers()->pluck('users.id'))->toEqualCanonicalizing($assignees->pluck('id'));
});

/**
 * @see \App\Http\Controllers\TaskController::assign
 * @see \App\Policies\TaskPolicy::assign
 */
test('他人が作成したタスクにはアサインできない', function () {
    // Arrange
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $token = $intruder->createToken('test')->plainTextToken;

    $task = Task::factory()->for($owner)->create();
    $targetUser = User::factory()->create();

    // Act
    $response = $this->withToken($token)->postJson("/api/tasks/{$task->id}/assign", [
        'user_ids' => [$targetUser->id],
    ]);

    // Assert
    $response->assertForbidden();
});
