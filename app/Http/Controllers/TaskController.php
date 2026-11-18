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
    $isManager = auth()->user()->hasAnyRole(['manager', 'super_admin']);

    $dashboardTasks = $isManager
        ? Task::query()
        : Task::where(function ($query) {
            $query->whereHas('todoList', function ($listQuery) {
                $listQuery->where('user_id', auth()->id());
            })->orWhere('assigned_to', auth()->id());
        });

    $taskStats = [
        'total' => (clone $dashboardTasks)->count(),

        'in_progress' => (clone $dashboardTasks)
            ->where('status', 'İşlem Görüyor')
            ->count(),

        'completed' => (clone $dashboardTasks)
            ->where(function ($query) {
                $query->where('is_completed', true)
                    ->orWhereIn('status', ['Tamamlandı', 'Çözüldü']);
            })
            ->count(),

        'overdue' => (clone $dashboardTasks)
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['Tamamlandı', 'Çözüldü'])
            ->where('is_completed', false)
            ->count(),
    ];

    $managerTasks = Task::whereHas('assignedBy', function ($query) {
        $query->role('manager');
    })
    ->whereNull('parent_task_id')
    ->latest()
    ->get();
    $notifications = auth()->user()->unreadNotifications;
    $recentActivities = auth()->user()
        ->notifications()
        ->latest()
        ->limit(5)
        ->get();
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
        'notifications',
        'managerTasks',
        'taskStats' ,
        'recentActivities'
    ));
}

    public function myTasks()
    {
        $tasks = Task::where(function ($query) {
            $query->where('assigned_to', auth()->id())
                ->orWhereHas('todoList', function ($listQuery) {
                    $listQuery->where('user_id', auth()->id());
                });
        })
            ->with(['assignedBy', 'todoList'])
            ->latest()
            ->get()
            ->groupBy(function ($task) {
                return (int) $task->assigned_by === (int) auth()->id()
                    && $task->assigned_to === null
                        ? 'created'
                        : $task->assignment_status;
            });

        return view('tasks.mine', compact('tasks'));
    }

    public function allTasks()
    {
        abort_unless(
            auth()->user()->hasAnyRole(['manager', 'super_admin']),
            403
        );

        $allTasks = Task::with([
            'todoList',
            'assignedUser',
            'assignedBy'
        ])
            ->latest()
            ->paginate(15);

        return view('tasks.all', compact('allTasks'));
    }

    public function create()
{
    $todoLists = TodoList::where('user_id', auth()->id())
        ->latest()
        ->get();

    $users = User::orderBy('name')->get();

    return view('tasks.create', compact(
        'todoLists',
        'users'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
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
            'assigned_to' => auth()->user()->hasRole('employee')
                ? null
                : $request->assigned_to,

            'assigned_by' => auth()->id(),
            'parent_task_id' => null,
            'assignment_status' => auth()->user()->hasRole('employee')
                ? 'accepted'
                : ($request->assigned_to ? 'pending' : 'accepted'),
        ]);

        if ($task->assignedUser) {
            $task->assignedUser->notify(
                new TaskAssignedNotification($task)
            );
        }

        return redirect()->route('tasks.index')->with(
            'success',
            $task->assigned_to
                ? 'Görev başarıyla atandı.'
                : 'Görev başarıyla oluşturuldu.'
        );
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

public function show(Task $task)
{
    $isOwner = (int) $task->todoList?->user_id === (int) auth()->id();

    $isAssignee = (int) $task->assigned_to === (int) auth()->id();

    $isSuperAdmin = auth()->user()->hasRole('super_admin');

    $isManager = auth()->user()->hasRole('manager');

abort_unless($isOwner || $isAssignee || $isSuperAdmin || $isManager, 403);
    $task->load([
        'todoList',
        'assignedUser',
        'assignedBy',
        'comments.user',
        'comments.replies.user',
    ]);

    $feedbacks = auth()->user()->notifications()
        ->where('type', TaskFeedbackNotification::class)
        ->where('data->task_id', $task->id)
        ->latest()
        ->get();

    return view('tasks.show', compact('task', 'feedbacks'));
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


    abort_unless(
        $isOwner
        || (
            $isAcceptedAssignee
            && ($canEditTitle || $canChangeDueDate)
        ),
        403
    );

   $rules = [];

    if ($isOwner) {

        $rules['title'] = ['required', 'string', 'max:255'];
        $rules['due_date'] = ['nullable', 'date'];
        $rules['assigned_to'] = ['nullable', 'exists:users,id'];

    } else {

        if ($canEditTitle) {
            $rules['title'] = ['required', 'string', 'max:255'];
        }

        if ($canChangeDueDate) {
            $rules['due_date'] = ['nullable', 'date'];

            if ($request->due_date != $task->due_date) {
                $rules['change_note'] = ['required', 'string', 'max:1000'];
            }
        }

    }                                       

    $validated = $request->validate(
        $rules,
        [
            'change_note.required' =>
                'Termin tarihini değiştirdiğinizde değişiklik notu girmeniz zorunludur.',
        ]
    );

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
            ? ($validated['title'] ?? $task->title)
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
        $changeNote = $validated['change_note'] ?? null;

        User::role('manager')->each(function ($manager) use ($task, $changes, $changeNote) {
            $manager->notify(
                new TaskUpdatedNotification(
                    $task,
                    auth()->user(),
                    $changes,
                    $changeNote
                )
            );
        });
    }

    return redirect()->route(
        auth()->user()->hasRole('employee') ? 'tasks.mine' : 'tasks.index'
    )->with(
        'success',
        'Görev başarıyla güncellendi.'
    );
}

public function updateStatus(Request $request, Task $task)
{
    $isOwner = (int) $task->todoList?->user_id === (int) auth()->id();

    $isAcceptedAssignee =
        (int) $task->assigned_to === (int) auth()->id()
        && $task->assignment_status === 'accepted';

    $isSuperAdmin = auth()->user()->hasRole('super_admin');

abort_unless($isOwner || $isAcceptedAssignee || $isSuperAdmin, 403);
    $validated = $request->validate([
        'status' => [
            'required',
            'in:Yeni,Planlama Bekleniyor,İçerik Bekleniyor,Cevap Bekliyor,İşlem Görüyor,Çözüldü,Tamamlandı',
        ],
    ]);

    $task->update([
        'status' => $validated['status'],
    ]);

    return back()->with('success', 'Görev durumu güncellendi.');
}

   public function destroy(Task $task)
{
    $this->authorizeTaskOwner($task);

    $task->delete();

    return redirect()->back()->with(
        'success',
        'Görev başarıyla silindi.'
    );
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

    return redirect()->back()->with(
        'success',
        'Görev kabul edildi.'
    );
}

public function reject(Request $request, Task $task)
{
    abort_if($task->assigned_to !== auth()->id(), 403);
    $request->validate(
        [
            'rejection_note' => 'required|string|max:300',
        ],
        [
            'rejection_note.required' => 'Reddetme nedeni boş bırakılamaz.',
            'rejection_note.string' => 'Reddetme nedeni geçerli bir metin olmalıdır.',
            'rejection_note.max' => 'Reddetme nedeni en fazla 300 karakter olabilir.',
        ]
    );

    $task->update([
        'assignment_status' => 'rejected',
    ]);
    if ($task->assignedBy) {
        $task->assignedBy->notify(
            new TaskStatusChangedNotification(
                $task,
                'rejected',
                $request->rejection_note
            )
        );
    }

    return redirect()->back()->with(
        'success',
        'Görev reddedildi.'
    );
}

public function sendFeedback(Request $request, Task $task)
{
    abort_unless(auth()->user()->can('send feedback'), 403);

    $isAcceptedAssignee =
        (int) $task->assigned_to === (int) auth()->id()
        && $task->assignment_status === 'accepted';

    $isOwner = (int) $task->todoList?->user_id === (int) auth()->id();

    abort_unless($isOwner || $isAcceptedAssignee, 403);

    $request->validate([
        'feedback' => 'required|string|max:300',
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
        'success',
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