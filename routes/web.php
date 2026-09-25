<?php

use App\Http\Controllers\TodoListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CommentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/lists', [TodoListController::class, 'store']) ->name('lists.store');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/lists/{list}/edit', [TodoListController::class, 'edit'])->name('lists.edit');
    Route::put('/lists/{list}', [TodoListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [TodoListController::class, 'destroy'])->name('lists.destroy');
    Route::patch('/tasks/{task}/accept', [TaskController::class, 'accept'])->name('tasks.accept');
    Route::patch('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::patch('/notifications/read', function () {auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();})->name('notifications.read');
    Route::get('/notifications/count', function () {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.count');
    Route::get('/notifications/dropdown', function () {
    $notifications = auth()->user()->Notifications;
    return view('partials.notifications', compact('notifications'));
    })->middleware('auth');
    Route::post('/tasks/{task}/feedback', [TaskController::class, 'sendFeedback'])->name('tasks.feedback');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])
    ->name('comments.store');
    });
    Route::get('/tasks/{task}/comments', function (\App\Models\Task $task) {
        $task->load([
            'comments.user',
            'comments.replies.user',
        ]);

        return view('partials.comments-list', compact('task'));
    })->name('comments.list');

require __DIR__.'/auth.php';