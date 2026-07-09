<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görevi Düzenle</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <div class="d-flex justify-content-between">

                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
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