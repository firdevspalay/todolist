<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'status' => $this->status,
            'user' => auth()->user()->name,

            'message' => match ($this->status) {
                'accepted' =>
                    auth()->user()->name . ' görevi kabul etti.',

                'rejected' =>
                    auth()->user()->name . ' görevi reddetti.',

                'completed' =>
                    auth()->user()->name . ' görevi tamamladı.',

                default => '',
            },
        ];
    }
}