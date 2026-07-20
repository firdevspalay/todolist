<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staj To-Do List</title>

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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            {{-- Kullanıcı bilgisi --}}
           <div class="d-flex justify-content-between align-items-center mb-3">

    <span class="text-muted">
        Hoş geldin,
        <strong>{{ auth()->user()->name }}</strong>
    </span>

    <div class="d-flex align-items-center gap-2">
        @if(auth()->user()->hasRole('manager'))
            <a
                href="{{ route('employees.index') }}"
                class="btn btn-sm btn-primary"
            >
                Çalışan Yönetimi
            </a>
        @endif

        <div class="dropdown">

            <button
                class="btn btn-outline-secondary position-relative"
                data-bs-toggle="dropdown"
            >
                <i class="bi bi-bell"></i>

                @if($notifications->count())
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $notifications->count() }}
                    </span>
                @endif

            </button>

            <ul class="dropdown-menu dropdown-menu-end" style="width:320px;">
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

                    <li class="px-3 py-2 border-bottom">
                        <strong>
                            {{ $notification->data['assigned_by'] ?? $notification->data['user'] ?? 'Sistem' }}
                        </strong><br>
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
                    </li>

                @empty

                    <li class="px-3 py-2 text-muted">
                        Bildirim bulunmuyor.
                    </li>

                @endforelse

            </ul>

        </div>
        <a
            href="{{ route('profile.edit') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-person me-1"></i>
            Profil
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button
                type="submit"
                class="btn btn-sm btn-outline-secondary"
            >
                <i class="bi bi-box-arrow-right me-1"></i>
                Çıkış Yap
            </button>
        </form>

    </div>

</div>

            {{-- Başlık --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center bg-primary text-white rounded">
                    <h3 class="mb-0">
                        <i class="bi bi-check2-square me-2"></i>
                        Yapılacaklar Listesi
                    </h3>
                </div>
            </div>

            {{-- Bekleyen görev atamaları --}}
            @if($assignedTasks->count())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="bi bi-inbox me-2"></i>
                            Bana Atanan Görevler
                        </h5>

                        @foreach($assignedTasks as $task)
                            <div class="border rounded p-3 mb-3">
                                <div class="fw-semibold mb-1">
                                    {{ $task->title }}
                                </div>

                                @if($task->due_date)
                                    <small class="text-muted d-block mb-1">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                    </small>
                                @endif

                                @if($task->assignedBy)
                                    <small class="text-primary d-block">
                                        <i class="bi bi-person-fill me-1"></i>
                                        Atayan: {{ $task->assignedBy->name }}
                                    </small>
                                @endif

                                @if($task->todoList)
                                    <small class="text-secondary d-block mt-1">
                                        <i class="bi bi-folder2-open me-1"></i>
                                        Liste: {{ $task->todoList->name }}
                                    </small>
                                @endif

                                <div class="mt-3 d-flex gap-2">
                                    <form
                                        action="{{ route('tasks.accept', $task->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                        >
                                            <i class="bi bi-check-lg me-1"></i>
                                            Kabul Et
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('tasks.reject', $task->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                        >
                                            <i class="bi bi-x-lg me-1"></i>
                                            Reddet
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Kabul edilen görevler --}}
           @if($acceptedTasks->count())
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="bi bi-check2-circle me-2 text-success"></i>
                Kabul Ettiğim Görevler
            </h5>

            @foreach($acceptedTasks as $task)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">

                        <div class="flex-grow-1">
                            @if($task->is_completed)
                                <div class="fw-semibold mb-1 text-decoration-line-through text-muted">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    {{ $task->title }}
                                </div>

                                <small class="text-success d-block mb-1">
                                    Tamamlandı
                                </small>
                            @else
                                <div class="fw-semibold mb-1">
                                    {{ $task->title }}
                                </div>
                            @endif

                            @if($task->due_date)
                                @php
                                    $acceptedDueDate = \Carbon\Carbon::parse($task->due_date);
                                    $acceptedDaysLeft = now()
                                        ->startOfDay()
                                        ->diffInDays($acceptedDueDate, false);
                                @endphp

                                @if($acceptedDaysLeft < 0)
                                    <small class="text-danger d-block">
                                        <i class="bi bi-calendar-x me-1"></i>
                                        {{ $acceptedDueDate->format('d.m.Y') }}
                                        • Termin geçti
                                    </small>
                                @elseif($acceptedDaysLeft <= 3)
                                    <small class="text-warning d-block">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $acceptedDueDate->format('d.m.Y') }}
                                        • {{ $acceptedDaysLeft }} gün kaldı
                                    </small>
                                @else
                                    <small class="text-success d-block">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ $acceptedDueDate->format('d.m.Y') }}
                                        • {{ $acceptedDaysLeft }} gün kaldı
                                    </small>
                                @endif
                            @endif

                            @if($task->assignedBy)
                                <small class="text-primary d-block mt-1">
                                    <i class="bi bi-person-fill me-1"></i>
                                    Atayan: {{ $task->assignedBy->name }}
                                </small>
                            @endif

                            @if($task->todoList)
                                <small class="text-secondary d-block mt-1">
                                    <i class="bi bi-folder2-open me-1"></i>
                                    Liste: {{ $task->todoList->name }}
                                </small>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            @if(
                                auth()->user()->can('edit task title')
                                || auth()->user()->can('change due date')
                            )
                                <a
                                    href="{{ route('tasks.edit', $task->id) }}"
                                    class="btn btn-sm btn-outline-warning"
                                    title="Görevi düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif

                        <form
                            action="{{ route('tasks.toggle', $task->id) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-sm {{ $task->is_completed ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                title="{{ $task->is_completed ? 'Tamamlanmadı olarak işaretle' : 'Tamamlandı olarak işaretle' }}"
                            >
                                <i class="bi {{ $task->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                            </button>
                        </form>
                    </div>

                    </div>
                    @can('send feedback')
                        <form
                            action="{{ route('tasks.feedback', $task->id) }}"
                            method="POST"
                            class="mt-3"
                        >
                            @csrf

                            <div class="input-group">
                                <input
                                    type="text"
                                    name="feedback"
                                    class="form-control"
                                    placeholder="Görev hakkında geri bildirim yazın..."
                                    maxlength="1000"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="btn btn-outline-primary"
                                >
                                    <i class="bi bi-send me-1"></i>
                                    Gönder
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
    </div>
@endif
@if($rejectedTasks->count())
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <h5 class="mb-3">
                <i class="bi bi-x-circle me-2 text-danger"></i>
                Reddettiğim Görevler
            </h5>

            @foreach($rejectedTasks as $task)

                <div class="border rounded p-3 mb-3">

                    <div class="fw-semibold mb-1">
                        {{ $task->title }}
                    </div>

                    @if($task->due_date)
                        <small class="text-muted d-block">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                        </small>
                    @endif

                    @if($task->assignedBy)
                        <small class="text-primary d-block mt-1">
                            <i class="bi bi-person-fill me-1"></i>
                            Atayan: {{ $task->assignedBy->name }}
                        </small>
                    @endif

                    @if($task->todoList)
                        <small class="text-secondary d-block mt-1">
                            <i class="bi bi-folder2-open me-1"></i>
                            Liste: {{ $task->todoList->name }}
                        </small>
                    @endif

                    <small class="text-danger d-block mt-2">
                        <i class="bi bi-x-circle-fill me-1"></i>
                        Bu görevi reddettiniz.
                    </small>

                </div>

            @endforeach

        </div>
    </div>
@endif

            {{-- Yeni liste oluşturma --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Yeni Liste Oluştur</h5>

                    <form action="{{ route('lists.store') }}" method="POST">
                        @csrf

                        <div class="input-group">
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Liste adı..."
                                required
                            >

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                <i class="bi bi-folder-plus me-1"></i>
                                Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Kullanıcının listeleri --}}
            @if($todoLists->count())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Listelerim</h5>

                        @foreach($todoLists as $list)
                            <div class="card mb-3">

                                {{-- Liste başlığı --}}
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="fw-bold">
                                        <i class="bi bi-folder2-open me-2"></i>
                                        {{ $list->name }}
                                    </div>

                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-light border-0"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            aria-label="Liste işlemleri"
                                        >
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('lists.edit', $list->id) }}"
                                                >
                                                    <i class="bi bi-pencil me-2"></i>
                                                    Düzenle
                                                </a>
                                            </li>

                                            <li>
                                                <form
                                                    action="{{ route('lists.destroy', $list->id) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item text-danger"
                                                        onclick="return confirm('Bu liste ve içindeki görevler silinsin mi?')"
                                                    >
                                                        <i class="bi bi-trash me-2"></i>
                                                        Sil
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Görevler --}}
                                <ul class="list-group list-group-flush">
                                    @forelse($list->tasks as $task)
                                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3">

                                            <div class="flex-grow-1">

                                                {{-- Görev adı --}}
                                                @if($task->is_completed)
                                                    <div class="text-decoration-line-through text-muted text-break">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        {{ $task->title }}
                                                    </div>
                                                @else
                                                    <div class="text-break">
                                                        <i class="bi bi-circle text-muted me-2"></i>
                                                        {{ $task->title }}
                                                    </div>
                                                @endif

                                                {{-- Termin tarihi --}}
                                                @if($task->due_date)
                                                    @php
                                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                        $daysLeft = now()
                                                            ->startOfDay()
                                                            ->diffInDays($dueDate, false);
                                                    @endphp

                                                    @if($daysLeft < 0)
                                                        <small class="text-danger ms-4">
                                                            <i class="bi bi-calendar-x me-1"></i>
                                                            {{ $dueDate->format('d.m.Y') }}
                                                            • Termin geçti
                                                        </small>
                                                    @elseif($daysLeft <= 3)
                                                        <small class="text-warning ms-4">
                                                            <i class="bi bi-calendar-event me-1"></i>
                                                            {{ $dueDate->format('d.m.Y') }}
                                                            • {{ $daysLeft }} gün kaldı
                                                        </small>
                                                    @else
                                                        <small class="text-success ms-4">
                                                            <i class="bi bi-calendar-check me-1"></i>
                                                            {{ $dueDate->format('d.m.Y') }}
                                                            • {{ $daysLeft }} gün kaldı
                                                        </small>
                                                    @endif
                                                @endif

                                                {{-- Atanan kişi --}}
                                                @if($task->assignedUser)
                                                    <br>

                                                    <small class="text-primary ms-4">
                                                        <i class="bi bi-person-fill me-1"></i>
                                                        Atanan: {{ $task->assignedUser->name }}
                                                    </small>
                                                @endif
                                                @if($task->assignment_status === 'pending')
                                                     <br>
                                                    <small class="text-warning ms-4">
                                                          <i class="bi bi-hourglass-split me-1"></i>
                                                 Yanıt bekleniyor
                                                      </small>
                                                @elseif($task->assignment_status === 'accepted')
                                                    <br>
                                                    <small class="text-success ms-4">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Kabul edildi
                                                    </small>
                                                @elseif($task->assignment_status === 'rejected')
                                                    <br>
                                                    <small class="text-danger ms-4">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                 Reddedildi
                                                    </small>
                                                @endif
                                            </div>

                                            {{-- Görev butonları --}}
                                            <div class="d-flex flex-shrink-0 gap-1">
                                                <form
                                                    action="{{ route('tasks.toggle', $task->id) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm {{ $task->is_completed ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                                    >
                                                        <i class="bi {{ $task->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                                    </button>
                                                </form>

                                                @if(!$task->is_completed)
                                                    <a
                                                        href="{{ route('tasks.edit', $task->id) }}"
                                                        class="btn btn-sm btn-outline-warning"
                                                    >
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif

                                                <form
                                                    action="{{ route('tasks.destroy', $task->id) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Bu görev silinsin mi?')"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>

                                    @empty
                                        <li class="list-group-item text-muted">
                                            Henüz görev yok.
                                        </li>
                                    @endforelse
                                </ul>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Yeni görev oluşturma --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Yeni Görev Oluştur</h5>

                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="todo_list_id" class="form-label">
                                Liste
                            </label>

                            <select
                                id="todo_list_id"
                                name="todo_list_id"
                                class="form-select"
                                required
                            >
                                <option value="">Liste seçin...</option>

                                @foreach($todoLists as $list)
                                    <option value="{{ $list->id }}">
                                        {{ $list->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Görev
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                class="form-control"
                                placeholder="Yeni bir görev yazın..."
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">
                                Termin Tarihi
                            </label>

                            <input
                                id="due_date"
                                type="date"
                                name="due_date"
                                class="form-control"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">
                                Atanacak Kişi
                            </label>

                            <select
                                id="assigned_to"
                                name="assigned_to"
                                class="form-select"
                            >
                                <option value="">Kimse</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            <i class="bi bi-plus-circle me-1"></i>
                            Görev Ekle
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>