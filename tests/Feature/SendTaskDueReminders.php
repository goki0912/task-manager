<?php

use App\Jobs\SendTaskDueReminders;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * @see \App\Jobs\SendTaskDueReminders::handle
 */
test('タスクの期限に達したら作成者とアサインユーザーに通知が送られる', function () {
    // Arrange
    Http::fake();
    Carbon::setTestNow(now());

    $creator = User::factory()->create(['line_user_id' => 'U_creator']);
    $assignee = User::factory()->create(['line_user_id' => 'U_assignee']);

    $task = Task::factory()->create([
        'user_id' => $creator->id,
        'title' => 'LINE通知テストタスク',
        'due_date' => now()->addMinutes(30),
        'remind_before_minutes' => 40, // → now() で通知条件成立
        'is_reminded' => false,
    ]);

    $task->assignedUsers()->attach($assignee->id);

    // Act
    dispatch(new SendTaskDueReminders());

    // Assert
    Http::assertSent(static function ($request) {
        return $request->url() === 'https://api.line.me/v2/bot/message/push';
    });

    expect($task->fresh()->is_reminded)->toEqual(true);
});

/**
 * @see \App\Jobs\SendTaskDueReminders::handle
 */
test('すでに通知済みのタスクには通知が送られない', function () {
    // Arrange
    Http::fake();
    Carbon::setTestNow(now());

    $creator = User::factory()->create(['line_user_id' => 'U_creator']);

    Task::factory()->create([
        'user_id' => $creator->id,
        'title' => '通知済みタスク',
        'due_date' => now()->addMinutes(30),
        'remind_before_minutes' => 30,
        'is_reminded' => true, // → もう通知済み
    ]);

    // Act
    dispatch(new SendTaskDueReminders());

    // Assert
    Http::assertNothingSent();
});
