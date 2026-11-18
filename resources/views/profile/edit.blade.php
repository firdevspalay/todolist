<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Otomatik Yenileme
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Sayfanın güncel kalması için otomatik yenileme ayarlarını yönetin.
                            </p>
                        </header>

                        <form method="POST" action="{{ route('profile.refresh-settings') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        name="auto_refresh_enabled"
                                        value="1"
                                        {{ auth()->user()->auto_refresh_enabled ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    >

                                    <span class="text-sm text-gray-700">
                                        Otomatik yenileme açık
                                    </span>
                                </label>
                            </div>

                            <div>
                                <label for="refresh_interval" class="block text-sm font-medium text-gray-700">
                                    Yenileme sıklığı
                                </label>

                                <select
                                    id="refresh_interval"
                                    name="refresh_interval"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    @foreach ([15 => '15 saniye', 30 => '30 saniye', 60 => '1 dakika', 120 => '2 dakika', 300 => '5 dakika'] as $seconds => $label)
                                        <option
                                            value="{{ $seconds }}"
                                            {{ auth()->user()->refresh_interval == $seconds ? 'selected' : '' }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                Ayarları Kaydet
                            </button>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
