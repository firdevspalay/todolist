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

<body class="bg-light">

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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            <a
                href="{{ route('tasks.index') }}"
                class="btn btn-outline-secondary btn-sm mb-3"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Geri
            </a>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="mb-0 text-break">
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

                    <hr class="my-4">

                    <h5 class="mb-3">
                        <i class="bi bi-chat-left-text me-2"></i>
                        Yorumlar
                    </h5>

                    <div id="commentsList">
                        @include('partials.comments-list')
                    </div>

                    <hr>

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

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>
                            Gönder
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

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