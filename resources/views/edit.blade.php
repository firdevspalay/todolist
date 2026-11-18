<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Görevi Düzenle</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
</head>

<body style="background:#f5f7fb; min-height:100vh;">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('layouts.navigation')

    <main class="lg:ml-64">

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white"
                            style="background: linear-gradient(135deg, #4338ca, #6366f1); padding: 20px 24px;">
                            <h4 class="mb-0">
                                <i class="bi bi-pencil-square me-2"></i>
                                Görevi Düzenle
                            </h4>
                        </div>

                        <div class="card-body">

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form
                                action="{{ route('tasks.update', $task->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PUT')
                                
                                @php
                                    $isOwner = (int) auth()->id() === (int) $task->todoList?->user_id;

                                    $isAcceptedAssignee =
                                        auth()->id() === $task->assigned_to
                                        && $task->assignment_status === 'accepted';

                                    $canEditTitle =
                                        $isOwner
                                        || (
                                            $isAcceptedAssignee
                                            && auth()->user()->can('edit task title')
                                        );

                                    $canChangeDueDate =
                                        $isOwner
                                        || (
                                            $isAcceptedAssignee
                                            && auth()->user()->can('change due date')
                                        );
                                @endphp


                                {{-- GÖREV BAŞLIĞI --}}
                                <div class="mb-3">
                                    <label class="form-label">Görev</label>

                                    @if($canEditTitle)
                                        <input
                                            type="text"
                                            name="title"
                                            value="{{ old('title', $task->title) }}"
                                            class="form-control"
                                            required
                                        >
                                    @else
                                        <input
                                            type="text"
                                            value="{{ $task->title }}"
                                            class="form-control"
                                            disabled
                                        >
                                    @endif
                                </div>


                                {{-- TERMİN TARİHİ --}}
                                <div class="mb-3">
                                    <label class="form-label">Termin Tarihi</label>

                                    @if($canChangeDueDate)
                                        <input
                                            type="date"
                                            name="due_date"
                                            value="{{ old('due_date', $task->due_date) }}"
                                            class="form-control"
                                        >
                                    @else
                                        <input
                                            type="date"
                                            value="{{ $task->due_date }}"
                                            class="form-control"
                                            disabled
                                        >
                                    @endif
                                </div>


                                {{-- EMPLOYEE TERMİN DEĞİŞİKLİK NOTU --}}
                                @if($isAcceptedAssignee && $canChangeDueDate)
                                    <div class="mb-3">
                                        <label for="change_note" class="form-label">
                                            Değişiklik Notu
                                        </label>

                                        <textarea
                                            id="change_note"
                                            name="change_note"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Termin tarihini değiştirme nedeninizi yazın..."
                                        >{{ old('change_note') }}</textarea>
                                    </div>
                                @endif


                                {{-- GÖREV ATAMA: SADECE GÖREV SAHİBİ --}}
                                @if($isOwner)
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
                                                <option
                                                    value="{{ $user->id }}"
                                                    @selected(
                                                        old('assigned_to', $task->assigned_to)
                                                        == $user->id
                                                    )
                                                >
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif


                                <div class="d-flex justify-content-between">
                                    <a
                                        href="{{ url()->previous() }}"
                                        class="btn btn-secondary"
                                    >
                                        Geri
                                    </a>

                                    <button type="submit" class="btn text-white"
                                            style="background:#4f46e5; border-radius:8px; padding:9px 20px;">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Kaydet
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>
</html>