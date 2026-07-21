<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskFeedbackNotification;
use App\Notifications\TaskStatusChangedNotification;
use App\Notifications\TaskUpdatedNotification;

class TaskController extends Controller
{
    public function index()
{
    $todoLists = TodoList::where('user_id', auth()->id())
                         ->with(['tasks' => function ($query) {
                            $query->with('assignedUser')
                                ->latest();
                         }])
                         ->latest()
                         ->get();
    $users = User::orderBy('name')->get();
    $notifications = auth()->user()->unreadNotifications;
    $assignedTasks = Task::where('assigned_to', auth()->id())
    ->where('assignment_status', 'pending')
    ->with('assignedBy')
    ->latest()
    ->get();
    $acceptedTasks = Task::where('assigned_to', auth()->id())
    ->where('assignment_status', 'accepted')
    ->with(['assignedBy', 'todoList'])
    ->latest()
    ->get();
    $rejectedTasks = Task::where('assigned_to', auth()->id())
    ->where('assignment_status', 'rejected')
    ->with(['assignedBy', 'todoList'])
    ->latest()
    ->get();
    return view('welcome', compact(
    'todoLists',
    'users',
    'assignedTasks',
    'acceptedTasks',
    'rejectedTasks',
    'notifications'
));
}

    public function store(Request $request)
{
   $request->validate([
    'title' => 'required',
    'todo_list_id' => 'required|exists:todo_lists,id',
    'due_date' => 'nullable|date',
    'assigned_to' => 'nullable|exists:users,id',
]);
    $todoList = TodoList::where('id', $request->todo_list_id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

    $task = Task::create([
    'title' => $request->title,
    'due_date' => $request->due_date,
    'todo_list_id' => $todoList->id,
    'is_completed' => false,

    'assigned_to' => $request->assigned_to,
    'assigned_by' => auth()->id(),

    'assignment_status' => $request->assigned_to
        ? 'pending'
        : 'accepted',
]);
if ($task->assignedUser) {
    $task->assignedUser->notify(
        new TaskAssignedNotification($task)
    );
}
    return redirect()->back();
}

   public function toggle(Task $task)
{
    $isOwner =
        $task->todoList
        && (int) $task->todoList->user_id === (int) auth()->id();

    $isAcceptedAssignee =
        (int) $task->assigned_to === (int) auth()->id()
        && $task->assignment_status === 'accepted';

    abort_unless($isOwner || $isAcceptedAssignee, 403);

    $willBeCompleted = ! $task->is_completed;

    $task->update([
        'is_completed' => $willBeCompleted,
    ]);

    if ($willBeCompleted && $isAcceptedAssignee && $task->assignedBy) {
        $task->assignedBy->notify(
            new TaskStatusChangedNotification($task, 'completed')
        );
    }

    return redirect()->back();
}

public function edit(Task $task)
{
    $isOwner =
        $task->todoList
        && (int) $task->todoList->user_id === (int) auth()->id();

    $isAcceptedAssignee =
        (int) $task->assigned_to === (int) auth()->id()
        && $task->assignment_status === 'accepted';

    $hasEditPermission =
        auth()->user()->can('edit task title')
        || auth()->user()->can('change due date');

    abort_unless(
        $isOwner || ($isAcceptedAssignee && $hasEditPermission),
        403
    );

    $users = User::orderBy('name')->get();

    return view('edit', compact('task', 'users'));
}

public function update(Request $request, Task $task)
{
    $isOwner =
        $task->todoList
        && (int) $task->todoList->user_id === (int) auth()->id();

    $isAcceptedAssignee =
        (int) $task->assigned_to === (int) auth()->id()
        && $task->assignment_status === 'accepted';

    $canEditTitle =
        $isOwner
        || auth()->user()->can('edit task title');

    $canChangeDueDate =
        $isOwner
        || auth()->user()->can('change due date');

    abort_unless(
        $isOwner
        || (
            $isAcceptedAssignee
            && ($canEditTitle || $canChangeDueDate)
        ),
        403
    );

    $rules = [];

    if ($canEditTitle) {
        $rules['title'] = ['required', 'string', 'max:255'];
    }

    if ($canChangeDueDate) {
        $rules['due_date'] = ['nullable', 'date'];
    }

    if ($isOwner) {
        $rules['assigned_to'] = ['nullable', 'exists:users,id'];
    }

    $validated = $request->validate($rules);

    $oldAssignedTo = $task->assigned_to;
    $oldTitle = $task->title;
    $oldDueDate = $task->due_date;
    $newAssignedTo = $isOwner
        ? (
            $request->filled('assigned_to')
                ? (int) $request->assigned_to
                : null
        )
        : $task->assigned_to;

    $assignmentChanged =
        $isOwner
        && (int) $oldAssignedTo !== (int) $newAssignedTo;

    $task->update([
        'title' => $canEditTitle
            ? $validated['title']
            : $task->title,

        'due_date' => $canChangeDueDate
            ? ($validated['due_date'] ?? null)
            : $task->due_date,

        'assigned_to' => $newAssignedTo,

        'assigned_by' => $assignmentChanged
            ? ($newAssignedTo ? auth()->id() : null)
            : $task->assigned_by,

        'assignment_status' => $assignmentChanged
            ? ($newAssignedTo ? 'pending' : 'accepted')
            : $task->assignment_status,
    ]);

    if ($assignmentChanged && $newAssignedTo) {
        $task->load('assignedUser');

        $task->assignedUser?->notify(
            new TaskAssignedNotification($task)
        );
    }
    $changes = [];

    if ($canEditTitle && $oldTitle !== $task->title) {
        $changes[] = 'the task title';
    }

    if ($canChangeDueDate && $oldDueDate != $task->due_date) {
        $changes[] = 'the due date';
    }

    if (!$isOwner && !empty($changes)) {
        User::role('manager')->each(function ($manager) use ($task, $changes) {
            $manager->notify(
                new TaskUpdatedNotification(
                    $task,
                    auth()->user(),
                    $changes
                )
            );
        });
    }

    return redirect()->route('tasks.index');
}

   public function destroy(Task $task)
{
    $this->authorizeTaskOwner($task);

    $task->delete();

    return redirect()->back();
}

   public function accept(Task $task)
{
    abort_if($task->assigned_to !== auth()->id(), 403);

    $task->update([
        'assignment_status' => 'accepted',
    ]);
    if ($task->assignedBy) {
    $task->assignedBy->notify(
        new TaskStatusChangedNotification($task, 'accepted')
    );
}

    return redirect()->back();
}

public function reject(Task $task)
{
    abort_if($task->assigned_to !== auth()->id(), 403);

    $task->update([
        'assignment_status' => 'rejected',
    ]);
    if ($task->assignedBy) {
    $task->assignedBy->notify(
        new TaskStatusChangedNotification($task, 'rejected')
    );
}

    return redirect()->back();
}

public function sendFeedback(Request $request, Task $task)
{
    abort_unless(auth()->user()->can('send feedback'), 403);

    $request->validate([
        'feedback' => 'required|string|max:1000',
    ]);

    if ($task->assignedBy) {
        $task->assignedBy->notify(
            new TaskFeedbackNotification(
                $task,
                $request->feedback
            )
        );
    }

    return redirect()->back()->with(
        'status',
        'Geri bildirim başarıyla gönderildi.'
    );
}

private function authorizeTaskOwner(Task $task): void
{
    abort_if(
        $task->todoList->user_id !== auth()->id(),
        403
    );
}
}