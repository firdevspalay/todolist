<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $employee,
        public array $changes
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "{$this->employee->name} ";

        if (count($this->changes) === 2) {
            $message .= "görev başlığını ve termin tarihini güncelledi.";
        } elseif ($this->changes[0] === 'the task title') {
            $message .= "görev başlığını güncelledi.";
        } else {
            $message .= "termin tarihini güncelledi.";
        }

        return [
            'task_id' => $this->task->id,
            'message' => $message,
        ];
    }
}