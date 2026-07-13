<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        TodoList::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back();
    }

    public function edit(TodoList $list)
{
    abort_if($list->user_id !== auth()->id(), 403);

    return view('lists.edit', compact('list'));
}

public function update(Request $request, TodoList $list)
{
    abort_if($list->user_id !== auth()->id(), 403);

    $request->validate([
        'name' => 'required|max:255'
    ]);

    $list->update([
        'name' => $request->name
    ]);

    return redirect()->route('tasks.index');
}

public function destroy(TodoList $list)
{
    abort_if($list->user_id !== auth()->id(), 403);

    $list->delete();

    return redirect()->route('tasks.index');
}

}
