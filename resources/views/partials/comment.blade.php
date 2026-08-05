<div class="border rounded p-3 mb-3">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <strong>
            {{ $comment->user->name }}
        </strong>

        <small class="text-muted">
            {{ $comment->created_at->format('d.m.Y H:i') }}
        </small>

    </div>

    <div class="mb-2">
        {{ $comment->body }}
    </div>

   <button
        type="button"
        class="btn btn-sm btn-outline-primary reply-button"
        data-comment="{{ $comment->id }}"
    >
        <i class="bi bi-reply me-1"></i>
        Cevapla
    </button>

    <div
        id="reply-form-{{ $comment->id }}"
        class="mt-3"
        style="display:none;"
    >

        <form action="{{ route('comments.store', $task) }}" method="POST">

            @csrf

            <input
                type="hidden"
                name="parent_id"
                value="{{ $comment->id }}"
            >

            <div class="mb-2">
                <textarea
                    name="body"
                    class="form-control"
                    rows="2"
                    placeholder="Cevabınızı yazın..."
                    required
                ></textarea>
            </div>

            <button class="btn btn-sm btn-primary">
                Gönder
            </button>

        </form>

    </div>

    @if($comment->replies->count())

        <div
            class="mt-3 border-start ps-3"
            style="margin-left: clamp(12px, 3vw, 24px);"
        >

            @foreach($comment->replies as $reply)

                @include('partials.comment', [
                    'comment' => $reply,
                    'task' => $task
                ])

            @endforeach

        </div>

    @endif

</div>