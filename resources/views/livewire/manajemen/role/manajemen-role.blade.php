<div class="space-y-6">

    <!-- HERO -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main to-blue-deep p-6 shadow-sm">
        <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 h-28 w-28 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="hidden h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur md:flex">
                    <iconify-icon icon="solar:shield-keyhole-bold" width="34" height="34"></iconify-icon>
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white capitalize">
                        Manajemen Role Akses
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Tambah role baru dan atur hak aksesnya langsung dari database.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Total Role
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    {{ $roles->count() }} Role
                </h2>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">

        <!-- FORM -->
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">
                        <iconify-icon icon="solar:folder-with-files-bold" width="28" height="28"></iconify-icon>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $editingRoleId ? 'Ubah Role' : 'Tambah Role' }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Pilih akses yang boleh dibuka untuk role ini.
                        </p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="space-y-6 p-6">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Nama Role
                    </label>

                    <input
                        type="text"
                        wire:model.defer="name"
                        placeholder="Contoh: Kurikulum"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                    @error('name')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700">
                                Hak Akses
                            </h3>

                            <p class="mt-1 text-xs text-gray-500">
                                Centang menu yang ingin diberikan ke role ini.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click.prevent="$wire.set('permissions', @js($availablePermissions))"
                            class="rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-main transition hover:bg-blue-100">
                            Pilih semua
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($permissionGroups as $groupName => $groupPermissions)
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-800">
                                        {{ $groupName }}
                                    </h4>

                                    <span class="text-xs text-gray-500">
                                        {{ count($groupPermissions) }} akses
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @foreach ($groupPermissions as $permission)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm transition hover:border-blue-main hover:bg-blue-50/60">
                                            <input
                                                type="checkbox"
                                                value="{{ $permission }}"
                                                wire:model="permissions"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-main focus:ring-blue-main" />

                                            <span class="capitalize text-gray-700">
                                                {{ \Illuminate\Support\Str::headline($permission) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('permissions')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-blue-main px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                        {{ $editingRoleId ? 'Perbarui Role' : 'Simpan Role' }}
                    </button>

                    @if ($editingRoleId)
                        <button
                            type="button"
                            wire:click="resetForm"
                            class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-blue-main hover:text-blue-main">
                            Batal Edit
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Daftar Role
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Role yang tersimpan akan langsung dipakai oleh middleware akses.
                        </p>
                    </div>

                    <div class="w-full lg:max-w-sm">
                        <input
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari role..."
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-center font-semibold">No</th>
                            <th class="px-6 py-4 text-left font-semibold">Role</th>
                            <th class="px-6 py-4 text-left font-semibold">Hak Akses</th>
                            <th class="px-6 py-4 text-center font-semibold">User</th>
                            <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($roles as $role)
                            <tr class="border-t border-gray-100 transition hover:bg-gray-50">
                                <td class="px-6 py-5 text-center font-medium text-gray-700">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 font-bold uppercase text-blue-main">
                                            {{ substr($role->name, 0, 1) }}
                                        </div>

                                        <div>
                                            <h3 class="font-semibold capitalize text-gray-800">
                                                {{ $role->name }}
                                            </h3>

                                            @if ($role->id === 1)
                                                <p class="text-xs text-amber-600">
                                                    Role sistem dilindungi
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach (array_slice($role->permissions ?? [], 0, 3) as $permission)
                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold capitalize text-blue-700">
                                                {{ \Illuminate\Support\Str::headline($permission) }}
                                            </span>
                                        @endforeach

                                        @if (count($role->permissions ?? []) > 3)
                                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                                +{{ count($role->permissions ?? []) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex rounded-full bg-emerald-100 px-4 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $role->users_count }} user
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="editRole({{ $role->id }})"
                                            @disabled($role->id === 1)
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-50">
                                            <iconify-icon icon="lineicons:pencil-1" width="20" height="20"></iconify-icon>
                                        </button>

                                        <button
                                            type="button"
                                            @click="$dispatch('open-delete-role', { id: {{ $role->id }} })"
                                            @disabled($role->id === 1 || $role->users_count > 0)
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-50">
                                            <iconify-icon icon="lineicons:trash-3" width="20" height="20"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <livewire:components.modal.manajemen.role.modal-hapus-role />

</div>