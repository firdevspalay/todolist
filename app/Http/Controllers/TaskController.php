<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::latest()->get();
        return view('welcome', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
        'title' => 'required'        
        ]);

        Task::create([
            'title' => $request->title,
            'is_completed' => false
        ]);

        return redirect()->back();
    }

    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return redirect()->back();
    }

   public function edit(Task $task)
{
    return view('edit', compact('task'));
}

public function update(Request $request, Task $task)
{
    $request->validate([
        'title' => 'required'   
     ]);

    $task->update([
        'title' => $request->title,
    ]);

    return redirect()->route('tasks.index');
}

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }
}