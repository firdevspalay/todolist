<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Notifications\TaskCommentNotification;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        if (auth()->user()->hasRole('employee')) {
            abort_unless(auth()->user()->can('comment on task'), 403);
        }

        $isOwner = (int) $task->todoList?->user_id === (int) auth()->id();

        $isAssignee = (int) $task->assigned_to === (int) auth()->id();

        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        abort_unless($isOwner || $isAssignee || $isSuperAdmin, 403);

        $validated = $request->validate(
            [
                'body' => ['required', 'string', 'max:2000'],
                'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            ],
            [
                'body.required' =>
                    'Yorum veya cevap alanı boş bırakılamaz.',
            ]
        );

        if (!empty($validated['parent_id'])) {
            $parentBelongsToTask = Comment::where(
                'id',
                $validated['parent_id']
            )
                ->where('task_id', $task->id)
                ->exists();

            abort_unless($parentBelongsToTask, 422);
        }

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $recipients = collect([
            $task->assignedBy,
            $task->assignedUser,
        ])
            ->filter()
            ->unique('id')
            ->reject(fn ($user) => $user->id === auth()->id());

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new TaskCommentNotification($task, $comment, auth()->user())
            );
        }

        return back()->with(
            'success',
            !empty($validated['parent_id'])
                ? 'Cevabınız gönderildi.'
                : 'Yorumunuz gönderildi.'
        );
    }
}