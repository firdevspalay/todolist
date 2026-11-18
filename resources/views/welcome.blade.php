<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TaskFlow</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
            color: #172033;
            min-height: 100vh;
        }
    </style>
</head>

<body>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('layouts.navigation')

        
    @if (session('success'))
        <div
            id="success-toast"
            class="position-fixed top-0 end-0 m-4 alert alert-success shadow"
            style="z-index: 9999;"
        >
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('success-toast');

                if (toast) {
                    toast.remove();
                }
            }, 3000);
        </script>
    @endif
    @if ($errors->any())
        <div
            id="error-toast"
            class="position-fixed top-0 end-0 m-4 alert alert-danger shadow"
            style="z-index: 9999;"
        >
            {{ $errors->first() }}
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('error-toast');

                if (toast) {
                    toast.remove();
                }
            }, 5000);
        </script>
    @endif

   

<main class="lg:ml-64">
    <div class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="fw-bold mb-0 text-dark">Dashboard</h5>
            <button type="button"
                    class="btn btn-sm rounded-3"
                    style="background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe;"
                    data-bs-toggle="modal"
                    data-bs-target="#newListModal">
                <i class="bi bi-plus-lg me-1"></i>
                Yeni Liste
            </button>

            <a href="{{ route('tasks.create') }}"
            class="btn btn-sm rounded-3 text-white"
            style="background:#4338ca; border:1px solid #4338ca;">
                <span class="me-1 fw-bold">+</span>
                Yeni Görev
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="input-group" style="width: 280px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" id="taskSearch"
                    class="form-control bg-light border-start-0"
                    placeholder="Görevlerde ara..."
                    aria-label="Görevlerde ara">
            </div>
        <div class="dropdown">
            <button id="notificationBell"
                    class="btn btn-light rounded-circle position-relative"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="Bildirimler">
                <i class="bi bi-bell fs-5"></i>

                <span id="notificationCount"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ auth()->user()->unreadNotifications()->count() == 0 ? 'd-none' : '' }}">
                    {{ auth()->user()->unreadNotifications()->count() }}
                </span>
            </button>

            <div id="notificationDropdown"
                class="dropdown-menu dropdown-menu-end shadow border-0 p-3"
                style="width: 350px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                <span class="text-muted">Bildirimler yükleniyor...</span>
            </div>
        </div>
    <div class="d-flex align-items-center gap-2 border-start ps-3">
        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center"
            style="width: 36px; height: 36px;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
                <span class="fw-semibold text-dark">
                    {{ auth()->user()->name }}
                </span>
            </div>
            </div>
        </div>

        <div class="container-fluid px-4 px-lg-5 py-4">
   <div class="row justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-9">


            {{-- Dashboard Başlığı --}}
            <div class="mb-4 p-4 rounded-4"
                style="background: linear-gradient(120deg, #4338ca, #6366f1); color: white;">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <div>
                        <p class="mb-2 opacity-75">TASKFLOW / DASHBOARD</p>

                        <h2 class="fw-bold mb-2">
                            Görev Yönetimi
                        </h2>

                        <p class="mb-0 opacity-75">
                            Hoş geldin, {{ auth()->user()->name }}.
                            Görevlerini tek bir yerden yönet.
                        </p>
                    </div>
                </div>
            </div>


            {{-- Dashboard İstatistikleri --}}
                <div class="row g-3 mb-4">

                    @php
                        $statCards = [
                            [
                                'title' => 'Toplam Görev',
                                'value' => $taskStats['total'],
                                'icon' => 'bi-list-task',
                                'color' => '#4f46e5',
                                'bg' => '#eef2ff',
                            ],
                            [
                                'title' => 'Devam Eden',
                                'value' => $taskStats['in_progress'],
                                'icon' => 'bi-arrow-repeat',
                                'color' => '#d97706',
                                'bg' => '#fffbeb',
                            ],
                            [
                                'title' => 'Tamamlanan',
                                'value' => $taskStats['completed'],
                                'icon' => 'bi-check-circle',
                                'color' => '#059669',
                                'bg' => '#ecfdf5',
                            ],
                            [
                                'title' => 'Geciken',
                                'value' => $taskStats['overdue'],
                                'icon' => 'bi-exclamation-triangle',
                                'color' => '#dc2626',
                                'bg' => '#fef2f2',
                            ],
                        ];
                    @endphp

                    @foreach($statCards as $card)
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100"
                                style="transition: transform .2s ease, box-shadow .2s ease;"
                                onmouseenter="this.style.transform='translateY(-4px)'"
                                onmouseleave="this.style.transform='translateY(0)'">

                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>
                                            <p class="text-secondary small fw-medium mb-2">
                                                {{ $card['title'] }}
                                            </p>

                                            <h2 class="fw-bold mb-0" style="color: {{ $card['color'] }}">
                                                {{ $card['value'] }}
                                            </h2>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center rounded-3"
                                            style="width: 48px; height: 48px; background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                                            <i class="bi {{ $card['icon'] }} fs-4"></i>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6 order-lg-2">   

            {{-- Son Aktiviteler --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-clock-history me-2 text-primary"></i>
                        Son Aktiviteler
                    </h5>

                    @forelse($recentActivities as $activity)
                        <div class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2">
                                <i class="bi bi-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-semibold">
                                    @if(!empty($activity->data['task_id']))
                                        <a href="{{ route('tasks.show', $activity->data['task_id']) }}"
                                        class="text-decoration-none text-dark fw-semibold">
                                            {{ $activity->data['message'] ?? $activity->data['title'] ?? 'Yeni bildirim' }}
                                        </a>
                                    @else
                                        {{ $activity->data['message'] ?? $activity->data['title'] ?? 'Yeni bildirim' }}
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $activity->created_at->locale('tr')->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">
                            Henüz aktivite bulunmuyor.
                        </p>
                    @endforelse
                </div>
            </div>

                </div>
                <div class="col-12 col-lg-6">
    

            {{-- Bekleyen görev atamaları --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-inbox text-primary"></i>
                            Bana Atanan Görevler
                            <span class="badge bg-primary-subtle text-primary rounded-pill ms-auto">
                                {{ $assignedTasks->count() }}
                            </span>
                        </h5>

                    @if($assignedTasks->isEmpty())
                        <div class="text-center py-4 px-3">
                            <i class="bi bi-check-circle text-secondary fs-1"></i>
                            <h6 class="fw-semibold mt-3">Bekleyen görev bulunmuyor</h6>
                            <p class="text-secondary small mb-0">
                                Şu anda yanıtlamanız gereken bir görev yok.
                                Yeni bir görev atandığında burada görüntülenecek.
                            </p>
                        </div>
                    @endif
                        @foreach($assignedTasks as $task)
                            <div class="task-search-item border-0 border-start border-3 border-primary rounded-3 px-3 py-2 mb-2 bg-white shadow-sm">

                                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">

                                    <div class="flex-grow-1">
                                        <a href="{{ route('tasks.show', $task) }}"
                                        class="text-decoration-none text-dark fw-semibold">
                                            <span class="task-title">{{ $task->title }}</span>
                                        </a>

                                        <div class="d-flex flex-wrap align-items-center gap-3 mt-1">
                                            @if($task->due_date)
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                                </small>
                                            @endif

                                            @if($task->assignedBy)
                                                <small class="text-primary">
                                                    <i class="bi bi-person-fill me-1"></i>
                                                    Atayan: {{ $task->assignedBy->name }}
                                                </small>
                                            @endif

                                            @if($task->todoList)
                                                <small class="text-secondary">
                                                    <i class="bi bi-folder2-open me-1"></i>
                                                    Liste: {{ $task->todoList->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <form action="{{ route('tasks.accept', $task->id) }}"
                                            method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-lg me-1"></i>
                                                Kabul Et
                                            </button>
                                        </form>

                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="document.getElementById('reject-form-{{ $task->id }}').style.display = 'block'; this.style.display = 'none';">
                                            <i class="bi bi-x-lg me-1"></i>
                                            Reddet
                                        </button>
                                    </div>
                                </div>

                                <form id="reject-form-{{ $task->id }}"
                                    action="{{ route('tasks.reject', $task->id) }}"
                                    method="POST"
                                    class="mt-2"
                                    style="display: none;">
                                    @csrf
                                    @method('PATCH')

                                    <textarea name="rejection_note"
                                            class="form-control mb-2"
                                            rows="2"
                                            placeholder="Reddetme nedeninizi yazın..."
                                            maxlength="300"
                                            required></textarea>

                                    <div class="text-end mt-1 mb-2">
                                        <small class="text-muted rejection-counter">0/300</small>
                                    </div>

                                    <button type="button"
                                            class="btn btn-light btn-sm me-2"
                                            onclick="location.reload()">
                                        Vazgeç
                                    </button>

                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-lg me-1"></i>
                                        Reddet
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>
    </div>
   
            {{-- Yeni Liste Modal --}}
            <div class="modal fade" id="newListModal" tabindex="-1"
                aria-labelledby="newListModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow">

                        <div class="modal-header border-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold" id="newListModalLabel">
                                <i class="bi bi-folder-plus text-primary me-2"></i>
                                Yeni Liste Oluştur
                            </h5>
                            <button type="button" class="btn-close"
                                    data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>

                        <form action="{{ route('lists.store') }}" method="POST">
                            @csrf

                            <div class="modal-body px-4">
                                <label for="newListName" class="form-label fw-semibold">
                                    Liste Adı
                                </label>
                                <input type="text" id="newListName" name="name"
                                    class="form-control rounded-3"
                                    placeholder="Liste adını girin..."
                                    required>
                            </div>

                            <div class="modal-footer border-0 px-4 pb-4">
                                <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">Vazgeç</button>
                                <button type="submit" class="btn text-white"
                                    style="background:#4338ca; border:1px solid #4338ca;">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Liste Oluştur
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            {{-- Kullanıcının listeleri --}}
            @if($todoLists->count())
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="fw-bold mb-1">Listelerim</h4>
                            <p class="text-secondary mb-0 small">
                                Görevlerini listelerine göre takip et.
                            </p>
                        </div>

                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                            {{ $todoLists->count() }} Liste
                        </span>
                    </div>

                    @foreach($todoLists as $list)
                        {{-- Liste kartı --}}
                        <div id="list-{{ $list->id }}" class="card border-0 shadow-sm mb-3 overflow-hidden rounded-4">

                            {{-- Liste başlığı --}}
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-3"
                                            style="width: 42px; height: 42px;">
                                            <i class="bi bi-folder2 text-primary fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                {{ $list->name }}
                                            </h5>

                                            <small class="text-muted fw-medium">
                                                {{ $list->tasks->count() }} görev
                                            </small>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button
                                            class="btn btn-light border-0 rounded-circle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            aria-label="Liste işlemleri"
                                        >
                                            <i class="bi bi-three-dots"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('lists.edit', $list->id) }}"
                                                >
                                                    <i class="bi bi-pencil me-2"></i>
                                                    Düzenle
                                                </a>
                                            </li>

                                            <li><hr class="dropdown-divider"></li>

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
                            </div>

                            {{-- Görevler --}}
                            <div class="card-body p-0">
                                @php
                                    $perPage = 5;
                                    $currentPage = max(1, (int) request()->query('page_' . $list->id, 1));
                                    $totalPages = max(1, (int) ceil($list->tasks->count() / $perPage));
                                    $currentPage = min($currentPage, $totalPages);

                                    $visibleTasks = $list->tasks->slice(
                                        ($currentPage - 1) * $perPage,
                                        $perPage
                                    );
                                @endphp

                                @forelse($visibleTasks as $task)

                                    <div class="px-4 py-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start gap-3">

                                            <div class="d-flex gap-3 flex-grow-1">

                                                <div class="pt-1">
                                                    @if($task->is_completed)
                                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                                    @else
                                                        <i class="bi bi-circle text-secondary fs-5"></i>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1">

                                                    {{-- Görev adı --}}
                                                    <a
                                                        href="{{ route('tasks.show', $task) }}"
                                                        class="text-decoration-none {{ $task->is_completed ? 'text-secondary text-decoration-line-through' : 'text-dark' }}"
                                                    >
                                                        <span class="fw-semibold">
                                                            {{ $task->title }}
                                                        </span>
                                                    </a>

                                                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2">

                                                        {{-- Termin tarihi --}}
                                                        @if($task->due_date)
                                                            @php
                                                                $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                                $daysLeft = now()
                                                                    ->startOfDay()
                                                                    ->diffInDays($dueDate, false);
                                                            @endphp

                                                            @if($daysLeft < 0)
                                                                <small class="text-danger">
                                                                    <i class="bi bi-calendar-x me-1"></i>
                                                                    {{ $dueDate->format('d.m.Y') }}
                                                                    · Termin geçti
                                                                </small>
                                                            @elseif($daysLeft <= 3)
                                                                <small class="text-warning">
                                                                    <i class="bi bi-calendar-event me-1"></i>
                                                                    {{ $dueDate->format('d.m.Y') }}
                                                                    · {{ $daysLeft }} gün kaldı
                                                                </small>
                                                            @else
                                                                <small class="text-secondary">
                                                                    <i class="bi bi-calendar-check me-1"></i>
                                                                    {{ $dueDate->format('d.m.Y') }}
                                                                    · {{ $daysLeft }} gün kaldı
                                                                </small>
                                                            @endif
                                                        @endif

                                                        {{-- Atanan kişi --}}
                                                        @if($task->assignedUser)
                                                            <small class="text-secondary">
                                                                <i class="bi bi-person me-1"></i>
                                                                {{ $task->assignedUser->name }}
                                                            </small>
                                                        @endif

                                                        {{-- Atama durumu --}}
                                                        @if($task->assigned_to && $task->assignment_status === 'pending')
                                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">
                                                                Yanıt bekleniyor
                                                            </span>
                                                        @elseif($task->assigned_to && $task->assignment_status === 'accepted')
                                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                                                Kabul edildi
                                                            </span>
                                                        @elseif($task->assigned_to && $task->assignment_status === 'rejected')
                                                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">
                                                                Reddedildi
                                                            </span>
                                                        @endif
                                                        <form action="{{ route('tasks.status', $task) }}" method="POST" class="mt-2">
                                                            @csrf
                                                            @method('PATCH')

                                                            <select name="status"
                                                                    class="form-select form-select-sm"
                                                                    onchange="this.form.submit()">
                                                                @foreach([
                                                                    'Yeni',
                                                                    'Planlama Bekleniyor',
                                                                    'İçerik Bekleniyor',
                                                                    'Cevap Bekliyor',
                                                                    'İşlem Görüyor',
                                                                    'Çözüldü',
                                                                    'Tamamlandı'
                                                                ] as $status)
                                                                    <option value="{{ $status }}" {{ $task->status === $status ? 'selected' : '' }}>
                                                                        {{ $status }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Görev butonları --}}
                                            <div class="d-flex flex-shrink-0 gap-2">

                                                <form
                                                    action="{{ route('tasks.toggle', $task->id) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-light border"
                                                        title="{{ $task->is_completed ? 'Tamamlanmadı olarak işaretle' : 'Görevi tamamla' }}"
                                                    >
                                                        <i class="bi {{ $task->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                                    </button>
                                                </form>

                                                @if(!$task->is_completed)
                                                    <a
                                                        href="{{ route('tasks.edit', $task->id) }}"
                                                        class="btn btn-sm btn-light border"
                                                        title="Görevi düzenle"
                                                    >
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif

                                                <form
                                                    action="{{ route('tasks.destroy', $task->id) }}"
                                                    method="POST"
                                                    class="delete-task-form"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-light border text-danger"
                                                        title="Görevi sil"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <div class="text-center py-5">
                                        <i class="bi bi-inbox text-secondary fs-3"></i>
                                        <p class="text-secondary mb-0 mt-2">
                                            Bu listede henüz görev yok.
                                        </p>
                                    </div>
                                @endforelse
                                @if($totalPages > 1)
                                    <div class="d-flex justify-content-center align-items-center gap-2 py-3">
                                        @for($page = 1; $page <= $totalPages; $page++)
                                            <a href="{{ request()->fullUrlWithQuery(['page_' . $list->id => $page]) }}#list-{{ $list->id }}"
                                            class="btn btn-sm {{ $currentPage == $page ? 'text-white' : 'btn-outline-secondary' }}"
                                            style="{{ $currentPage == $page ? 'background:#4f46e5; border-color:#4f46e5;' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endfor
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.getElementById('notificationDropdown');
    const badge = document.getElementById('notificationCount');
    const bell = document.getElementById('notificationBell');
    let isNotificationDropdownOpen = false;
    bell.addEventListener('shown.bs.dropdown', () => {
        isNotificationDropdownOpen = true;
    });

    bell.addEventListener('hidden.bs.dropdown', () => {
        isNotificationDropdownOpen = false;
        refreshNotifications();
    });
    bell.addEventListener('click', async () => {
        await fetch('{{ route('notifications.read') }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        badge.textContent = '0';
        badge.classList.add('d-none');
    });

    if (!dropdown || !badge) {
        console.error('Bildirim alanı bulunamadı.');
        return;
    }

    let lastNotificationCount =
        Number.parseInt(badge.textContent.trim(), 10) || 0;

    async function refreshNotifications() {
        try {
            const response = await fetch(
                '{{ url('/notifications/dropdown') }}',
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
                    'Bildirim isteği başarısız:',
                    response.status
                );
                return;
            }

            const html = await response.text();

            const temporaryContainer = document.createElement('div');
            temporaryContainer.innerHTML = html;

            const countResponse = await fetch('{{ route('notifications.count') }}');
            const countData = await countResponse.json();
            const newNotificationCount = countData.count;

            
            if (!isNotificationDropdownOpen) {
                dropdown.innerHTML = html;
            }
            badge.textContent = newNotificationCount;
            badge.classList.toggle(
                'd-none',
                newNotificationCount === 0
            );

            lastNotificationCount = newNotificationCount;
        } catch (error) {
            console.error(
                'Bildirim yenileme hatası:',
                error
            );
        }
    }

    refreshNotifications();
    setInterval(refreshNotifications, 5000);
});
    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!form.classList.contains('delete-task-form')) {
            return;
        }

        event.preventDefault();

        if (form.dataset.submitting === 'true') {
            return;
        }

        const confirmed = confirm('Bu görev silinsin mi?');

        if (!confirmed) {
            return;
        }

        form.dataset.submitting = 'true';

        const button = form.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
        }

        form.submit();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.feedback-textarea').forEach(textarea => {
        const counter = textarea
            .closest('div')
            .parentElement
            .querySelector('.feedback-counter');

        const updateCounter = () => {
            counter.textContent = `${textarea.value.length}/300`;
        };

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea[name="rejection_note"]').forEach(textarea => {
        const counter = textarea
            .closest('form')
            .querySelector('.rejection-counter');

        const updateCounter = () => {
            counter.textContent = `${textarea.value.length}/300`;
        };

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const parentTaskSelect = document.getElementById('parent_task_id');
    const assignedToArea = document.getElementById('assignedToArea');
    const assignedToSelect = document.getElementById('assigned_to');

    if (!parentTaskSelect || !assignedToArea || !assignedToSelect) return;

    function updateAssignedTo() {
        if (parentTaskSelect.value) {
            assignedToArea.style.display = 'none';
            assignedToSelect.value = '';
        } else {
            assignedToArea.style.display = '';
        }
    }

    parentTaskSelect.addEventListener('change', updateAssignedTo);
    updateAssignedTo();
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('taskSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const search = this.value.toLocaleLowerCase('tr-TR').trim();

        document.querySelectorAll('.task-search-item').forEach(function (card) {
            const text = (card.querySelector('.task-title')?.textContent || '').toLocaleLowerCase('tr-TR');
            card.style.display = text.includes(search) ? '' : 'none';
        });
    });
});
</script>


</body> 
</html>