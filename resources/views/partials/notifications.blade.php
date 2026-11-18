@if($notifications->count())
    <li class="px-3 py-2 border-bottom text-end">
        <form action="{{ route('notifications.read') }}" method="POST">
            @csrf
            @method('PATCH')

            <button
                type="submit"
                class="btn btn-link btn-sm text-decoration-none p-0"
            >
                Tümünü okundu işaretle
            </button>
        </form>
    </li>
@endif

@forelse($notifications as $notification)

    <li
        class="px-3 py-2 border-bottom {{ is_null($notification->read_at) ? 'bg-secondary-subtle' : '' }}"
        data-notification-item
    >
        @if(is_null($notification->read_at))
            <span
                class="d-inline-block rounded-circle bg-primary me-2"
                style="width: 8px; height: 8px;"
            ></span>
        @endif

        </strong>
        <br>

        @if(!empty($notification->data['task_id']))
            <a href="{{ route('tasks.show', $notification->data['task_id']) }}"
            class="text-decoration-none text-dark">
        @endif

        {{ $notification->data['message'] ?? 'Yeni bir bildirim var.' }}<br>

        <small class="text-muted">
            {{ $notification->data['title'] ?? '' }}
        </small>
        @if(!empty($notification->data['task_title']))
            <div class="small fw-semibold mt-1">
                Görev: {{ $notification->data['task_title'] }}
            </div>
        @endif

        @if(!empty($notification->data['comment']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    💬 Yorum
                </div>

                <div class="small text-secondary text-break">
                    "{{ $notification->data['comment'] }}"
                </div>
            </div>
        @endif

        @if(!empty($notification->data['task_id']))
            </a>
        @endif

        @if(isset($notification->data['feedback']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    💬 Geri Bildirim
                </div>

                <div
                    class="small text-secondary text-break"
                    style="overflow-wrap:anywhere; word-break:break-word;"
                >
                    "{{ $notification->data['feedback'] }}"
                </div>
            </div>
        @endif

        @if(!empty($notification->data['change_note']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    📝 Değişiklik Notu
                </div>

                <div
                    class="small text-secondary text-break"
                    style="overflow-wrap:anywhere; word-break:break-word;"
                >
                    "{{ $notification->data['change_note'] }}"
                </div>
            </div>
        @endif

        @if(!empty($notification->data['rejection_note']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    ❌ Reddetme Nedeni
                </div>

                <div
                    class="small text-secondary text-break"
                    style="overflow-wrap:anywhere; word-break:break-word;"
                >
                    "{{ $notification->data['rejection_note'] }}"
                </div>
            </div>
        @endif
    </li>

@empty

    <li class="px-3 py-2 text-muted">
        Bildirim bulunmuyor.
    </li>

@endforelse