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

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
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

                        @role('manager')

                            <div class="mb-3">
                                <label class="form-label">Görev</label>

                                <input
                                    type="text"
                                    name="title"
                                    value="{{ old('title', $task->title) }}"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Termin Tarihi</label>

                                <input
                                    type="date"
                                    name="due_date"
                                    value="{{ old('due_date', $task->due_date) }}"
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

                        @else

                            <div class="mb-3">
                                <label class="form-label">Görev</label>

                                <input
                                    type="text"
                                    value="{{ $task->title }}"
                                    class="form-control"
                                    disabled
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Termin Tarihi</label>

                                <input
                                    type="date"
                                    name="due_date"
                                    value="{{ old('due_date', $task->due_date) }}"
                                    class="form-control"
                                >
                            </div>

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

                        @endrole

                        <div class="d-flex justify-content-between">
                            <a
                                href="{{ route('tasks.index') }}"
                                class="btn btn-secondary"
                            >
                                Geri
                            </a>

                            <button type="submit" class="btn btn-warning">
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

</body>
</html>