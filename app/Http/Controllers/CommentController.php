<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
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

        $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with(
            'success',
            !empty($validated['parent_id'])
                ? 'Cevabınız gönderildi.'
                : 'Yorumunuz gönderildi.'
        );
    }
}