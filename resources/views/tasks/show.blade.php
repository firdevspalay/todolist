<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $task->title }} - Görev Detayı</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light" style="background-color: #f4f6fb !important;">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.navigation')

@if(session('success'))
    <div
        id="successToast"
        class="alert alert-success position-fixed top-0 end-0 m-3 shadow"
        style="z-index: 9999; min-width: 280px;"
    >
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div
        id="errorToast"
        class="alert alert-danger position-fixed top-0 end-0 m-3 shadow"
        style="z-index: 9999; min-width: 280px;"
    >
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        {{ $errors->first() }}
    </div>
@endif
<main class="lg:ml-64">
    <div class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-3">
        <h5 class="fw-bold mb-0 text-dark">Görev Detayı</h5>

        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center"
                style="width: 36px; height: 36px;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="fw-semibold text-dark">{{ auth()->user()->name }}</span>
        </div>
    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <a
                    href="{{ url()->previous() }}"
                    class="btn btn-outline-secondary btn-sm mb-3"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Geri
                </a>

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header text-white px-4 py-4 border-0"
                         style="background: linear-gradient(120deg, #4338ca, #6366f1);">
                        <h3 class="mb-0 text-break fw-bold fs-4">
                            <i class="bi bi-card-checklist me-2"></i>
                            {{ $task->title }}
                        </h3>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">
                                    Termin Tarihi
                                </div>

                                <div class="fw-semibold">
                                    <i class="bi bi-calendar-event me-1"></i>

                                    {{
                                        $task->due_date
                                            ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y')
                                            : 'Belirtilmedi'
                                    }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">
                                    Atayan
                                </div>

                                <div class="fw-semibold">
                                    <i class="bi bi-person-up me-1"></i>
                                    {{ $task->assignedBy?->name ?? 'Belirtilmedi' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">
                                    Atanan
                                </div>

                                <div class="fw-semibold">
                                    <i class="bi bi-person-check me-1"></i>
                                    {{ $task->assignedUser?->name ?? 'Kimse' }}
                                </div>
                            </div>
                        </div>

                        @can('send feedback')
                            @if($task->assigned_to == auth()->id() && $task->assignment_status === 'accepted')
                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">Geri Bildirim Gönder</h5>

                                <form action="{{ route('tasks.feedback', $task->id) }}" method="POST">
                                    @csrf

                                    <textarea name="feedback"
                                            class="form-control mb-3"
                                            placeholder="Görev hakkında geri bildirim yazın..."
                                            maxlength="300"
                                            rows="3"
                                            required></textarea>

                                    <button type="submit"
                                            class="btn text-white"
                                            style="background:#4338ca;">
                                        <i class="bi bi-send me-1"></i>
                                        Geri Bildirim Gönder
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @if($feedbacks->isNotEmpty())
                            <hr class="my-4">

                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-chat-dots me-2" style="color:#4338ca;"></i>
                                Geri Bildirim Geçmişi
                            </h5>

                            @foreach($feedbacks as $feedback)
                                @php
                                    $sender = str_replace(
                                        ' görev hakkında geri bildirim gönderdi.',
                                        '',
                                        $feedback->data['message']
                                    );
                                @endphp

                                <div class="rounded-3 border p-3 mb-2"
                                    style="background:#f5f5ff;">

                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width:34px;height:34px;background:#e0e7ff;color:#4338ca;">
                                            {{ mb_strtoupper(mb_substr($sender, 0, 1)) }}
                                        </div>

                                        <span class="fw-semibold">{{ $sender }}</span>

                                        <small class="text-muted ms-auto">
                                            {{ $feedback->created_at->copy()->timezone('Europe/Istanbul')->format('d.m.Y · H:i') }}
                                        </small>
                                    </div>

                                    <div class="text-break mt-2" style="padding-left:42px;">
                                        {{ $feedback->data['feedback'] }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-chat-left-text me-2"></i>
                            Yorumlar
                        </h5>

                        <div id="commentsList">
                            @include('partials.comments-list')
                        </div>

                        <hr>
                        @if(!auth()->user()->hasRole('employee') || auth()->user()->can('comment on task'))
                        <form
                            action="{{ route('comments.store', $task) }}"
                            method="POST"
                            class="prevent-double-submit"
                        >
                            @csrf

                            <div class="mb-3">
                                <textarea
                                    name="body"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Yorum yaz..."
                                    maxlength="2000"
                                    required
                                ></textarea>
                            </div>

                            <button type="submit" class="btn text-white" style="background:#4338ca;">
                                <i class="bi bi-send me-1"></i>
                                Gönder
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>    

<script>
document.addEventListener('click', function (event) {
    const button = event.target.closest('.reply-button');

    if (!button) {
        return;
    }

    event.preventDefault();

    const commentId = button.dataset.comment;
    const replyForm = document.getElementById(
        'reply-form-' + commentId
    );

    if (!replyForm) {
        return;
    }

    const isHidden =
        window.getComputedStyle(replyForm).display === 'none';

    replyForm.style.display = isHidden ? 'block' : 'none';

    if (isHidden) {
        replyForm.querySelector('textarea')?.focus();
    }
});

async function refreshComments() {
    const commentsList =
        document.getElementById('commentsList');

    if (!commentsList) {
        return;
    }

    const openReplyForm = Array.from(
        commentsList.querySelectorAll('[id^="reply-form-"]')
    ).some((form) => {
        return window.getComputedStyle(form).display !== 'none';
    });

    const userIsTyping =
        document.activeElement?.matches(
            '#commentsList textarea'
        );

    if (openReplyForm || userIsTyping) {
        return;
    }

    try {
        const response = await fetch(
            '{{ route('comments.list', $task) }}',
            {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            }
        );

        if (!response.ok) {
            console.error(
                'Yorumlar alınamadı:',
                response.status
            );

            return;
        }

        commentsList.innerHTML = await response.text();
    } catch (error) {
        console.error(
            'Yorum yenileme hatası:',
            error
        );
    }
}

setInterval(refreshComments, 5000);

setTimeout(() => {
    document.getElementById('successToast')?.remove();
    document.getElementById('errorToast')?.remove();
}, 3000);


document.addEventListener('submit', function (event) {
    const form = event.target.closest('.prevent-double-submit');

    if (!form) {
        return;
    }

    if (form.dataset.submitting === 'true') {
        event.preventDefault();
        return;
    }

    form.dataset.submitting = 'true';

    const submitButton = form.querySelector(
        'button[type="submit"], input[type="submit"]'
    );

    if (submitButton) {
        submitButton.disabled = true;

        if (submitButton.tagName === 'BUTTON') {
            submitButton.dataset.originalText =
                submitButton.innerHTML;

            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Gönderiliyor...';
        }
    }
});
</script>

</body>
</html>