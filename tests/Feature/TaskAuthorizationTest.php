<?php

use App\Models\User;
use App\Models\Task;


/**
 * @see \App\Http\Controllers\TaskController::show
 * @see \App\Policies\TaskPolicy::view
 */
test('他人のタスクは閲覧できない', function () {
    // Arrange
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $token = $intruder->createToken('test')->plainTextToken;
    $task = Task::factory()->for($owner)->create();

    // Act
    $response = $this->withToken($token)->getJson("/api/tasks/{$task->id}");

    // Assert
    $response->assertForbidden();
});

/**
 * @see \App\Http\Controllers\TaskController::update
 * @see \App\Policies\TaskPolicy::update
 */
test('他人のタスクは更新できない', function () {
    // Arrange
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $token = $intruder->createToken('test')->plainTextToken;
    $task = Task::factory()->for($owner)->create();

    // Act
    $response = $this->withToken($token)->putJson("/api/tasks/{$task->id}", [
        'title' => 'Unauthorized Update',
        'description' => 'Not allowed',
        'due_date' => now()->addDay()->toDateTimeString(),
    ]);

    // Assert
    $response->assertForbidden();
});

/**
 * @see \App\Http\Controllers\TaskController::destroy
 * @see \App\Policies\TaskPolicy::delete
 */
test('他人のタスクは削除できない', function () {
    // Arrange
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $token = $intruder->createToken('test')->plainTextToken;
    $task = Task::factory()->for($owner)->create();

    // Act
    $response = $this->withToken($token)->deleteJson("/api/tasks/{$task->id}");

    // Assert
    $response->assertForbidden();
});
