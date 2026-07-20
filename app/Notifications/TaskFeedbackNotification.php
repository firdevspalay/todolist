<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskFeedbackNotification extends Notification
{
    use Queueable;

    public $task;
    public $feedback;

    public function __construct(Task $task, string $feedback)
    {
        $this->task = $task;
        $this->feedback = $feedback;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'feedback' => $this->feedback,
            'message' => auth()->user()->name . ' görev hakkında geri bildirim gönderdi.',
        ];
    }
}