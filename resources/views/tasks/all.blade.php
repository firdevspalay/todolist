<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-7xl">

            <div class="mb-8">
                <p class="mb-2 text-sm font-medium text-indigo-600">
                    TaskFlow / Görev Yönetimi
                </p>

                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Tüm Görevler
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Ekibinizin görevlerini tek bir yerden takip edin.
                </p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <h2 class="font-semibold text-slate-900">
                        Görev Listesi
                    </h2>

                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $allTasks->total() }} Görev
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Görev</th>
                                <th class="px-6 py-4">Liste</th>
                                <th class="px-6 py-4">Oluşturan</th>
                                <th class="px-6 py-4">Atanan</th>
                                <th class="px-6 py-4">Durum</th>
                                <th class="px-6 py-4">Termin</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($allTasks as $task)
                                <tr class="transition hover:bg-indigo-50/40">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('tasks.show', $task) }}"
                                           class="font-semibold text-slate-900 hover:text-indigo-600">
                                            {{ $task->title }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $task->todoList?->name ?? 'Yok' }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $task->assignedBy?->name ?? 'Bilinmiyor' }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $task->assignedUser?->name ?? 'Atanmadı' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @php
                                            $status = $task->status ?? 'Yeni';

                                            $statusClass = match($status) {
                                                'Tamamlandı', 'Çözüldü' => 'bg-green-100 text-green-700',
                                                'İşlem Görüyor' => 'bg-amber-100 text-amber-700',
                                                'Cevap Bekliyor', 'İçerik Bekleniyor', 'Planlama Bekleniyor' => 'bg-orange-100 text-orange-700',
                                                default => 'bg-blue-100 text-blue-700',
                                            };
                                        @endphp

                                        <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $task->due_date
                                            ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y')
                                            : 'Belirtilmedi' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        Henüz görev bulunmuyor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-6 py-5">
                    {{ $allTasks->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>