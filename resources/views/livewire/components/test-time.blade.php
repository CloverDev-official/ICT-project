<div class="fixed right-5 bottom-5 z-[9999]">

        {{-- Floating button --}}
        <button
            type="button"
            wire:click="$toggle('open')"
            class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-white shadow-lg transition hover:bg-gray-800"
            title="Test waktu"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </button>

        {{-- Panel --}}
        @if ($open)
            <div
                class="absolute right-0 bottom-14 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-2xl"
            >
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">
                            Test Waktu
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Simulasikan tanggal dan waktu.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('open', false)"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-3">

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            wire:model="date"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    {{-- Waktu --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Waktu
                        </label>

                        <input
                            type="time"
                            wire:model="time"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    {{-- Action --}}
                    <div class="flex gap-2 pt-1">
                        <button
                            type="button"
                            wire:click="apply"
                            wire:loading.attr="disabled"
                            class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="apply">
                                Terapkan
                            </span>

                            <span wire:loading wire:target="apply">
                                Menerapkan...
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="resetTestTime"
                            wire:loading.attr="disabled"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
