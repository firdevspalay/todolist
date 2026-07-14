<?php

use App\Http\Controllers\TodoListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/lists', [TodoListController::class, 'store']) ->name('lists.store');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
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
});

require __DIR__.'/auth.php';