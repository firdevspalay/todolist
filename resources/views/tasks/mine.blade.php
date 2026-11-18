
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görevlerim - TaskFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background:#f4f6fb;">

@include('layouts.navigation')

<main class="lg:ml-64">

    <div class="bg-white border-bottom px-4 py-3">
        <h5 class="fw-bold mb-0">Görevlerim</h5>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <div class="rounded-4 p-4 mb-4 text-white"
                     style="background:linear-gradient(120deg,#4338ca,#6366f1);">
                    <h3 class="fw-bold mb-2">Görevlerim</h3>
                    <p class="mb-0 opacity-75">
                        Sana atanan ve kendi oluşturduğun görevleri buradan takip edebilirsin.
                    </p>
                </div>

                @php
                    $sections = [
                        'created' => [
                            'title' => 'Oluşturduğum Görevler',
                            'icon' => 'bi-plus-circle',
                            'color' => '#4338ca'
                        ],
                        'pending' => [
                            'title' => 'Bekleyen Görevler',
                            'icon' => 'bi-hourglass-split',
                            'color' => '#d97706'
                        ],
                        'accepted' => [
                            'title' => 'Kabul Ettiğim Görevler',
                            'icon' => 'bi-check-circle',
                            'color' => '#059669'
                        ],
                        'rejected' => [
                            'title' => 'Reddettiğim Görevler',
                            'icon' => 'bi-x-circle',
                            'color' => '#dc2626'
                        ],
                    ];
                @endphp

                @foreach($sections as $status => $section)

                    @php
                        $sectionTasks = $tasks->get($status, collect());
                    @endphp

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">

                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi {{ $section['icon'] }}"
                                   style="color:{{ $section['color'] }};"></i>

                                <h5 class="fw-bold mb-0">
                                    {{ $section['title'] }}
                                </h5>

                                <span class="badge rounded-pill ms-auto"
                                      style="background:#eef2ff;color:#4338ca;">
                                    {{ $sectionTasks->count() }}
                                </span>
                            </div>

                            @forelse($sectionTasks as $task)

                                @php
                                    $isCreated = $status === 'created';

                                    $isAcceptedAssignee =
                                        (int) $task->assigned_to === (int) auth()->id()
                                        && $task->assignment_status === 'accepted';

                                    $canEdit =
                                        $isCreated
                                        || (
                                            $isAcceptedAssignee
                                            && (
                                                auth()->user()->can('edit task title')
                                                || auth()->user()->can('change due date')
                                            )
                                        );

                                    $canComplete =
                                        $isCreated || $isAcceptedAssignee;
                                @endphp

                                <div class="border rounded-3 p-3 mb-2">

                                    <div class="d-flex justify-content-between align-items-start gap-3">

                                        <div class="flex-grow-1" style="min-width:0;">

                                            <a href="{{ route('tasks.show', $task) }}"
                                               class="text-decoration-none text-dark fw-semibold">
                                                {{ $task->title }}
                                            </a>

                                            <div class="d-flex flex-wrap gap-3 mt-2">

                                                @if($task->due_date)
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                                    </small>
                                                @endif

                                                @if($task->assignedBy)
                                                    <small class="text-primary">
                                                        <i class="bi bi-person me-1"></i>
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

                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">

                                            @if($canComplete)
                                                <form action="{{ route('tasks.toggle', $task) }}"
                                                      method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-light border"
                                                            title="{{ $task->is_completed ? 'Tamamlamayı Geri Al' : 'Tamamla' }}">
                                                        <i class="bi {{ $task->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($canEdit)
                                                <a href="{{ route('tasks.edit', $task) }}"
                                                   class="btn btn-sm btn-light border"
                                                   title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif

                                            @if($isCreated)
                                                <form action="{{ route('tasks.destroy', $task) }}"
                                                      method="POST"
                                                      class="m-0"
                                                      onsubmit="return confirm('Bu görevi silmek istediğine emin misin?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-light border text-danger"
                                                            title="Sil">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('tasks.show', $task) }}"
                                               class="btn btn-sm btn-light border"
                                               title="Görev Detayı">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>

                                        </div>

                                    </div>

                                    @if($status === 'pending')

                                        <div class="d-flex gap-2 mt-3">

                                            <form action="{{ route('tasks.accept', $task) }}"
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button class="btn btn-success btn-sm"
                                                        type="submit">
                                                    Kabul Et
                                                </button>
                                            </form>

                                            <button class="btn btn-outline-danger btn-sm"
                                                    type="button"
                                                    onclick="document.getElementById('reject-{{ $task->id }}').classList.toggle('d-none')">
                                                Reddet
                                            </button>

                                        </div>

                                        <form id="reject-{{ $task->id }}"
                                              action="{{ route('tasks.reject', $task) }}"
                                              method="POST"
                                              class="d-none mt-3">
                                            @csrf
                                            @method('PATCH')

                                            <textarea name="rejection_note"
                                                      class="form-control mb-2"
                                                      maxlength="300"
                                                      rows="2"
                                                      placeholder="Reddetme nedenini yaz..."
                                                      required></textarea>

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm">
                                                Reddetmeyi Onayla
                                            </button>
                                        </form>

                                    @endif

                                </div>

                            @empty

                                <p class="text-muted small mb-0">
                                    Bu kategoride görev bulunmuyor.
                                </p>

                            @endforelse

                        </div>
                    </div>

                @endforeach

            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
