@forelse($task->comments as $comment)

    @include('partials.comment', [
        'comment' => $comment,
        'task' => $task
    ])

@empty

    <div class="alert alert-light border">
        Henüz yorum yapılmadı.
    </div>

@endforelse