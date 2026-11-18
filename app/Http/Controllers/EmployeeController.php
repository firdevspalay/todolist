<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'super_admin']), 403);

        $employees = User::query()
            ->where('id', '!=', auth()->id())
            ->with(['roles', 'permissions'])
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'super_admin']), 403);
        abort_if($employee->id === auth()->id(), 403);

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:manager,employee,super_admin'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                \Illuminate\Validation\Rule::in(
                    \Spatie\Permission\Models\Permission::pluck('name')->all()
                ),
            ],
        ]);


        if ($validated['role'] === 'super_admin' || $employee->hasRole('super_admin')) {
            abort_unless(auth()->user()->hasRole('super_admin'), 403);
        }

        if (!auth()->user()->hasRole('super_admin')) {
            $allowedPermissions = [
                'edit task title',
                'change due date',
                'send feedback',
                'comment on task',
            ];

            abort_if(
                count(array_diff($validated['permissions'] ?? [], $allowedPermissions)) > 0,
                403
            );
        }
        
        $employee->syncRoles([$validated['role']]);

        if ($validated['role'] === 'employee') {
            $employee->syncPermissions($validated['permissions'] ?? []);
        } else {
            $employee->syncPermissions([]);
        }

        return back()->with(
            'status',
            "{$employee->name} için rol ve yetkiler başarıyla kaydedildi."
        );
    }
}