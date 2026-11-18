<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public Comment $comment,
        public User $commenter
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'commenter' => $this->commenter->name,
            'message' => $this->commenter->name . ' göreve yorum yaptı.',
            'comment' => $this->comment->body,
        ];
    }
}