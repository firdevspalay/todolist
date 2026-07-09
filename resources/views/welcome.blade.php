<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staj To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center bg-primary text-white rounded">
                        <h3 class="mb-0"><i class="bi bi-check2-square me-2"></i>Yapılacaklar Listesi</h3>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf 
                            <div class="input-group">
                                <input type="text" name="title" class="form-control" placeholder="Yeni bir görev yazın..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-plus-circle me-1"></i>Ekle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Görevler</h5>
                        
                        <ul class="list-group">
                            @forelse($tasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $task->is_completed ? 'bg-light' : '' }}">
                                    
                                    @if($task->is_completed)
                                        <span class="text-decoration-line-through text-muted">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>{{ $task->title }}
                                        </span>
                                    @else
                                        <span>
                                            <i class="bi bi-circle text-muted me-2"></i>{{ $task->title }}
                                        </span>
                                    @endif

                                    <div class="d-flex">
                                        <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="me-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $task->is_completed ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                <i class="bi {{ $task->is_completed ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                            </button>
                                        </form>
                                    @if(!$task->is_completed)
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">Henüz hiç görev eklenmedi.</li>
                            @endforelse
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>