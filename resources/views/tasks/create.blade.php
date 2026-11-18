<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Görev - TaskFlow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: #f5f7fb; color: #172033;">

@include('layouts.navigation')

<main class="lg:ml-64">
    <div class="d-flex align-items-center bg-white border-bottom px-4 py-3">
        <h5 class="fw-bold mb-0">Yeni Görev</h5>
    </div>

    <div class="container-fluid px-4 px-lg-5 py-4">
        <div class="mx-auto" style="max-width: 900px;">

            <a href="{{ route('tasks.index') }}"
               class="btn btn-outline-secondary btn-sm mb-4">
                <i class="bi bi-arrow-left me-1"></i> Dashboard'a Dön
            </a>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="p-4 text-white"
                     style="background: linear-gradient(120deg, #4338ca, #6366f1);">
                    <h4 class="fw-bold mb-1">Yeni Görev Oluştur</h4>
                    <p class="mb-0 opacity-75">
                        Görev detaylarını belirleyin ve ilgili kişiye atayın.
                    </p>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="todo_list_id" class="form-label fw-semibold">Liste</label>
                            <select id="todo_list_id" name="todo_list_id" class="form-select" required>
                                <option value="">Liste seçin...</option>
                                @foreach($todoLists as $list)
                                    <option value="{{ $list->id }}" @selected(old('todo_list_id') == $list->id)>
                                        {{ $list->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Görev Başlığı</label>
                            <input type="text" id="title" name="title"
                                   class="form-control" value="{{ old('title') }}"
                                   placeholder="Görev başlığını yazın..." required>
                        </div>

                        <div class="row g-3">
                            <div class="{{ auth()->user()->hasRole('employee') ? 'col-12' : 'col-md-6' }}">
                                <label for="due_date" class="form-label fw-semibold">Termin Tarihi</label>
                                <input type="date" id="due_date" name="due_date"
                                       class="form-control" value="{{ old('due_date') }}">
                            </div>

                            @if(!auth()->user()->hasRole('employee'))
                                <div class="col-md-6">
                                    <label for="assigned_to" class="form-label fw-semibold">Atanacak Kişi</label>
                                    <select id="assigned_to" name="assigned_to" class="form-select">
                                        <option value="">Kimse</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('tasks.index') }}" class="btn btn-light border">
                                Vazgeç
                            </a>
                            <button type="submit" class="btn px-4 text-white"
                                style="background:#4338ca; border:1px solid #4338ca;">
                                <i class="bi bi-plus-circle me-1"></i> Görev Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>