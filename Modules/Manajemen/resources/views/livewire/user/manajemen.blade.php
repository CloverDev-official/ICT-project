<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-sm">

        <!-- ornament -->
        <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 backdrop-blur text-white">

                    <iconify-icon
                        icon="solar:users-group-rounded-bold"
                        width="34"
                        height="34">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white capitalize">
                        Manajemen User
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola akun operator, wali kelas, dan pengawas.
                    </p>
                </div>

            </div>

            <!-- action -->
            <div class="sm:flex grid grid-cols-1  sm:flex-row gap-3">

                <!-- tambah -->
                <a href="{{ route('tambah-user') }}" wire:navigate>
                    <button
                        class="group w-full sm:w-auto rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">

                        <div class="flex items-center justify-center gap-2">

                            <iconify-icon
                                class="transition group-hover:rotate-90"
                                icon="line-md:plus"
                                width="22"
                                height="22">
                            </iconify-icon>

                            Tambah User

                        </div>

                    </button>
                </a>

            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm z-0">

        <!-- FILTER -->
        <div class="border-b border-gray-200 p-5">

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">

                <!-- ROLE -->
                <div
                    x-data="{
                        open: false,
                        selectedId: null,
                        selectedLabel: 'Semua Role',

                        select(id, label){
                            this.selectedId = id
                            this.selectedLabel = label
                            this.open = false

                            $wire.$set('roleId', id)
                        },

                        toggle(){
                            this.open = !this.open
                        }
                    }"
                    class="relative w-full">

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Filter Role
                    </label>

                    <!-- trigger -->
                    <div
                        @click="toggle()"
                        class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main">

                        <span x-text="selectedLabel" class="text-gray-700"></span>

                        <iconify-icon
                            class="text-gray-400 transition-transform"
                            :class="{ 'rotate-180': open }"
                            icon="lineicons:chevron-up"
                            width="20"
                            height="20">
                        </iconify-icon>

                    </div>

                    <!-- dropdown -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        style="display: none;"
                        class="absolute z-50 mt-2 w-full h-44 overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                        <!-- semua -->
                        <div
                            @click.prevent="select(null, 'Semua Role')"
                            class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                            <span>Semua Role</span>

                            <iconify-icon
                                x-show="selectedId === null"
                                icon="lineicons:check"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>
                        <!-- list -->
                        @foreach ($roles as $role)
                            <div
                                @click.prevent="select('{{ $role->id }}', '{{ $role->name }}')"
                                class="flex cursor-pointer items-center justify-between px-4 py-3 capitalize transition hover:bg-blue-main hover:text-white">

                                <span>{{ $role->name }}</span>

                                <iconify-icon
                                    x-show="selectedId === '{{ $role->id }}'"
                                    icon="lineicons:check"
                                    width="20"
                                    height="20">
                                </iconify-icon>

                            </div>

                        @endforeach

                    </div>
                </div>

                <!-- search -->
                <div class="lg:col-span-3">

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Cari User
                    </label>

                    <div class="relative">

                        <input
                            type="search"
                            name="search"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari nama user..."
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 py-3 pl-4 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                        <div
                            class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                            <iconify-icon
                                icon="mdi:account-search-outline"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Username
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Email
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Role
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)

                        @php
                            $statusValue = strtolower((string) $user->is_active === '1' ? 'aktif' : 'nonaktif');

                            $statusClass = match ($statusValue) {
                                'nonaktif' => 'bg-rose-100 text-rose-600',
                                default => 'bg-green-100 text-green-600',
                            };

                            $isSuperAdmin = $user->role_id === 1;
                            $toggleLabel = $user->is_active ? 'Nonaktifkan user' : 'Aktifkan user';
                            $toggleIcon = $user->is_active ? 'lineicons:ban-2' : 'lineicons:checkmark';
                            $toggleClass = $user->is_active
                                ? 'bg-rose-500 hover:bg-rose-600'
                                : 'bg-emerald-500 hover:bg-emerald-600';
                        @endphp

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            <!-- no -->
                            <td class="px-6 py-5 text-center font-medium text-gray-700">
                                {{ $loop->iteration }}
                            </td>

                            <!-- user -->
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">

                                        {{ substr($user->name, 0, 1) }}

                                    </div>

                                    <div>
                                        <h1 class="font-semibold capitalize text-gray-800">
                                            {{ $user->name }}
                                        </h1>

                                        <p class="text-xs text-gray-400">
                                            User account
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <!-- email -->
                            <td class="px-6 py-5 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <!-- role -->
                            <td class="px-6 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-blue-100 px-4 py-1 text-xs font-semibold capitalize text-blue-600">

                                    {{ $user->role->name }}
                                </div>

                            </td>

                            <!-- status -->
                            <td class="px-6 py-5 text-center">

                                <div class="flex items-center justify-center">

                                    <div
                                        class="{{ $statusClass }} inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold capitalize">

                                        <div class="h-2 w-2 rounded-full bg-current"></div>

                                        {{ $statusValue }}

                                    </div>

                                </div>

                            </td>

                            <!-- aksi -->
                            <td class="px-6 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    <!-- ban -->
                                    <button
                                        type="button"
                                        title="{{ $isSuperAdmin ? 'Super-admin tidak bisa dinonaktifkan' : $toggleLabel }}"
                                        @disabled($isSuperAdmin)
                                        onclick="return confirm('Ubah status user ini?')"
                                        wire:click="toggleStatus({{ $user->id }})"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl text-white shadow-sm transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60 {{ $toggleClass }}">

                                        <iconify-icon
                                            icon="{{ $toggleIcon }}"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                    <!-- edit -->
                                    <a href="{{ route('edit-user', ['userId' => $user->id]) }}" wire:navigate>

                                        <button
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-500">

                                            <iconify-icon
                                                icon="lineicons:pencil-1"
                                                width="20"
                                                height="20">
                                            </iconify-icon>

                                        </button>

                                    </a>

                                    <!-- delete -->
                                    <div">
                                        <button
                                            @disabled($isSuperAdmin)
                                            @click="$dispatch('open-delete-user', { id: {{ $user->id }} })"
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60 hover:-translate-y-0.5 hover:bg-rose-600">

                                            <iconify-icon
                                                icon="lineicons:trash-3"
                                                width="20"
                                                height="20">
                                            </iconify-icon>

                                        </button>

                                        
                                    </div>
                                    
                                </div>

                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>

        </div>
        
        <div
            class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            
            {{ $users->links('livewire.components.pagination') }}

        </div>

    </div>
    
    <livewire:manajemen.user.components.modal.hapus/>
</div>