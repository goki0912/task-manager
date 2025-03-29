<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTaskDueReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('[通知ジョブ] タスクを検索しています…');

        $now = now(); // UTC の now()

        $tasks = Task::with(['user', 'assignedUsers'])
            ->whereNotNull('due_date')
            ->whereNotNull('remind_before_minutes')
            ->where('is_reminded', false)
            ->get()
            ->filter(function ($task) use ($now) {
                $remindTime = $task->due_date->copy()->subMinutes($task->remind_before_minutes);

                Log::info('now (TZ) = ' . $now->timezoneName);
                Log::info('remindTime (TZ) = ' . $remindTime->timezoneName);
                Log::info('now (UTC) = ' . $now);
                Log::info('remindTime (UTC) = ' . $remindTime);
                Log::info('比較結果 = ' . ($now->greaterThanOrEqualTo($remindTime) ? 'true' : 'false'));

                return $now->greaterThanOrEqualTo($remindTime);
            });

        if ($tasks->isEmpty()) {
            Log::info('[通知ジョブ] 対象タスクなし');
            return;
        }

        foreach ($tasks as $task) {
            $message = "🔔 タスクの期限が迫っています：{$task->title}（{$task->due_date->format('Y-m-d H:i')}）";
            Log::info("[通知ジョブ] 通知対象タスク：{$task->title}");

            // 📌 重複のないユーザーリストを構築（作成者 + アサインユーザー）
            $usersToNotify = collect([$task->user])
                ->merge($task->assignedUsers)
                ->unique('id')
                ->filter(fn($user) => $user->line_user_id);

            foreach ($usersToNotify as $user) {
                Log::info("[通知ジョブ] 通知送信 → user_id={$user->id}, line_user_id={$user->line_user_id}");
                $this->sendLineMessage($user->line_user_id, $message);
            }

            $task->update(['is_reminded' => true]);
            Log::info("[通知ジョブ] is_reminded を true に更新：task_id={$task->id}");
        }
    }

    private function sendLineMessage(string $to, string $message): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.line.channel_access_token'),
            'Content-Type' => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $to,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('[通知ジョブ] LINE送信失敗', [
                'to' => $to,
                'message' => $message,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);
        } else {
            Log::info('[通知ジョブ] LINE送信成功');
        }
    }
}
