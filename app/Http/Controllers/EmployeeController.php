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
        abort_unless(auth()->user()?->hasRole('manager'), 403);

        $employees = User::query()
            ->where('id', '!=', auth()->id())
            ->with(['roles', 'permissions'])
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        abort_unless(auth()->user()?->hasRole('manager'), 403);
        abort_if($employee->id === auth()->id(), 403);

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:manager,employee'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                'in:edit task title,change due date,send feedback',
            ],
        ]);

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