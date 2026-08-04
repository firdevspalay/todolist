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

    <li class="px-3 py-2 border-bottom" data-notification-item>
        <strong>
            {{
                $notification->data['employee']
                ?? $notification->data['assigned_by']
                ?? $notification->data['user']
                ?? 'Sistem'
            }}
        </strong>
        <br>

        {{ $notification->data['message'] ?? 'Yeni bir bildirim var.' }}<br>

        <small class="text-muted">
            {{ $notification->data['title'] ?? '' }}
        </small>

        @if(isset($notification->data['feedback']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    💬 Geri Bildirim
                </div>

                <div class="small text-secondary">
                    "{{ $notification->data['feedback'] }}"
                </div>
            </div>
        @endif

        @if(!empty($notification->data['change_note']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    📝 Değişiklik Notu
                </div>

                <div class="small text-secondary">
                    "{{ $notification->data['change_note'] }}"
                </div>
            </div>
        @endif

        @if(!empty($notification->data['rejection_note']))
            <div class="mt-2 rounded bg-light p-2 border">
                <div class="fw-semibold text-dark mb-1">
                    ❌ Reddetme Nedeni
                </div>

                <div class="small text-secondary">
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