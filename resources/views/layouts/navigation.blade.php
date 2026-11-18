<nav x-data="{ open: false }">
    {{-- Desktop Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">
        {{-- Brand --}}
        <div class="flex h-20 items-center border-b border-slate-100 px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-sm">
                    T
                </div>

                <div>
                    <div class="text-base font-bold tracking-tight text-slate-900">
                        TaskFlow
                    </div>
                    <div class="text-xs text-slate-400">
                        Görev Yönetimi
                    </div>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 px-4 py-6">
            <div class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                Menü
            </div>

            <div class="space-y-1">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('dashboard', 'tasks.index')
                            ? 'bg-indigo-50 text-indigo-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6"/>
                    </svg>

                    Dashboard
                </a>

                @if(auth()->user()->hasRole('employee'))
                    <a
                        href="{{ route('tasks.mine') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('tasks.mine')
                                ? 'bg-indigo-50 text-indigo-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                        Görevlerim
                    </a>
                @endif
     
                @if(auth()->user()->hasAnyRole(['manager', 'super_admin']))
                    <a
                        href="{{ route('tasks.all') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('tasks.all')
                                ? 'bg-indigo-50 text-indigo-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>

                        Tüm Görevler
                    </a>
                @endif

                @if(auth()->user()->hasAnyRole(['manager', 'super_admin']))
                    <a
                        href="{{ route('employees.index') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('employees.*')
                                ? 'bg-indigo-50 text-indigo-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m8-4a4 4 0 10-8 0 4 4 0 008 0z"/>
                        </svg>

                        Çalışan Yönetimi
                    </a>
                @endif
            </div>
        </div>

        {{-- User --}}
        <div class="border-t border-slate-100 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-800">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="truncate text-xs text-slate-500">
                        {{ Auth::user()->email }}
                    </div>
                    @if(Auth::user()->hasRole('super_admin'))
                        <span class="inline-block mt-1 rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                            Super Admin
                        </span>
                    @elseif(Auth::user()->hasRole('manager'))
                        <span class="inline-block mt-1 rounded-md bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                            Manager
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <a
                    href="{{ route('profile.edit') }}"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-medium text-slate-600 transition hover:bg-white hover:text-slate-900"
                >
                    Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-white hover:text-red-600"
                    >
                        Çıkış
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Top Bar --}}
    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 font-bold text-white">
                T
            </div>

            <span class="font-bold text-slate-900">
                TaskFlow
            </span>
        </a>

        <button
            type="button"
            @click="open = !open"
            class="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
        >
            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>

            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
    >
        <div
            class="absolute inset-0 bg-slate-900/30"
            @click="open = false"
        ></div>

        <div class="absolute inset-y-0 left-0 w-72 bg-white p-5 shadow-xl">
            <div class="mb-8 flex items-center justify-between">
                <div class="font-bold text-slate-900">
                    TaskFlow
                </div>

                <button @click="open = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <div class="space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-medium transition-colors
                    {{ request()->routeIs('dashboard', 'tasks.index')
                        ? 'bg-indigo-50 text-indigo-600 font-semibold'
                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Dashboard
                </a>

                @if(auth()->user()->hasRole('employee'))
                    <a
                        href="{{ route('tasks.mine') }}"
                        class="block rounded-xl px-4 py-3 text-sm font-medium transition-colors
                            {{ request()->routeIs('tasks.mine')
                                ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        Görevlerim
                    </a>
                @endif

                @if(auth()->user()->hasAnyRole(['manager', 'super_admin']))
                    <a
                        href="{{ route('tasks.all') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors
                        {{ request()->routeIs('tasks.all')
                            ? 'bg-indigo-50 text-indigo-600 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        Tüm Görevler
                    </a>
                @endif

                @if(auth()->user()->hasAnyRole(['manager', 'super_admin']))
                    <a
                        href="{{ route('employees.index') }}"
                        class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Çalışan Yönetimi
                    </a>
                @endif

                <a
                    href="{{ route('profile.edit') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                    >
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>