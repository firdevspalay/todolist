<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Çalışan Yönetimi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-5 shadow-sm sm:p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="grid items-start gap-4 md:grid-cols-2">
                    @foreach ($employees as $employee)
                        @php
                            $roleName = $employee->roles->pluck('name')->first() ?? 'employee';
                            $roleLabel = $roleName === 'manager' ? 'Yönetici' : 'Çalışan';
                        @endphp

                        <details
                            class="group self-start overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4"
                            >
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-gray-900">
                                        {{ $employee->name }}
                                    </h3>

                                    <p class="mt-0.5 truncate text-sm text-gray-500">
                                        {{ $employee->email }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-2 pl-3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700"
                                    >
                                        {{ $roleLabel }}
                                    </span>

                                    <svg
                                        class="h-4 w-4 text-gray-400 transition-transform duration-200 group-open:rotate-180"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </summary>

                            <form method="POST" action="{{ route('employees.update', $employee) }}">
                                @csrf
                                @method('PUT')

                                <div class="border-t border-gray-200 px-5 py-4">
                                <div>
                                    <label
                                        for="role-{{ $employee->id }}"
                                        class="mb-1.5 block text-xs font-medium text-gray-600"
                                    >
                                        Rol
                                    </label>

                                   <select
                                        id="role-{{ $employee->id }}"
                                        name="role"
                                        class="w-28 rounded-md border-gray-300 py-1 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option
                                            value="manager"
                                            {{ $roleName === 'manager' ? 'selected' : '' }}
                                        >
                                            Yönetici
                                        </option>

                                        <option
                                            value="employee"
                                            {{ $roleName === 'employee' ? 'selected' : '' }}
                                        >
                                            Çalışan
                                        </option>
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <p class="mb-2.5 text-sm font-semibold text-gray-800">
                                        Yetkiler
                                    </p>

                                    <div class="space-y-2.5">
                                        <label class="flex cursor-pointer items-center gap-2.5">
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="edit task title"
                                                {{ $employee->hasPermissionTo('edit task title') ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />

                                            <span class="text-sm text-gray-700">
                                                Görev başlığını düzenleyebilir
                                            </span>
                                        </label>

                                        <label class="flex cursor-pointer items-center gap-2.5">
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="change due date"
                                                {{ $employee->hasPermissionTo('change due date') ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <span class="text-sm text-gray-700">
                                                Termin tarihini değiştirebilir
                                            </span>
                                        </label>

                                        <label class="flex cursor-pointer items-center gap-2.5">
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="send feedback"
                                                {{ $employee->hasPermissionTo('send feedback') ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <span class="text-sm text-gray-700">
                                                Geri bildirim gönderebilir
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-5 flex justify-end">
                                    <button
                                        type="submit"
                                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </div>
                            </form>
                        </details>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>