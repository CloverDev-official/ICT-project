<div class="mb-20 space-y-6">

    <!-- BACK -->
    <div>
        <a href="{{ route('manajemen-user') }}" wire:navigate>
            <button
                class="group flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-main hover:text-blue-main hover:shadow-md">

                <iconify-icon
                    icon="lineicons:chevron-left"
                    width="20"
                    height="20"
                    class="transition group-hover:-translate-x-1">
                </iconify-icon>

                Kembali

            </button>
        </a>
    </div>

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- glow -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:user-edit-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Edit User
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Perbarui informasi akun, role, dan relasi guru.
                    </p>
                </div>

            </div>

            <!-- badge -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Mode
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Update Data
                </h2>

            </div>

        </div>
    </div>

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Informasi Akun
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ubah data akun user. Password boleh dikosongkan jika tidak ingin diganti.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-600">

                <iconify-icon
                    icon="solar:pen-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Sedang diedit

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="update"
            class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <!-- USERNAME -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Username
                </label>

                <div class="relative">

                    <input
                        type="text"
                        wire:model.defer="name"
                        placeholder="examplename"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm capitalize transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                    <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                        <iconify-icon
                            icon="solar:user-bold"
                            width="22"
                            height="22">
                        </iconify-icon>
                    </div>

                </div>

                @error('name')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- EMAIL -->
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Email
                </label>

                <div class="relative">

                    <input
                        type="email"
                        wire:model.defer="email"
                        placeholder="contoh@gmail.com"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                    <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                        <iconify-icon
                            icon="solar:letter-bold"
                            width="22"
                            height="22">
                        </iconify-icon>
                    </div>

                </div>

                @error('email')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- PASSWORD -->
            <div x-data="{ showPassword: false }">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Password Baru
                    <span class="font-normal text-gray-400">
                        (Opsional)
                    </span>
                </label>

                <div class="relative">

                    <input
                        wire:model.defer="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Kosongkan jika tidak diganti"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-4 flex items-center text-gray-400 transition hover:text-blue-main">

                        <iconify-icon
                            :icon="showPassword ? 'ri:eye-fill' : 'iconoir:eye-closed'"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </button>

                </div>

                @error('password')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- PASSWORD CONFIRMATION -->
            <div x-data="{ showPassword: false }">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Ulangi Password Baru
                </label>

                <div class="relative">

                    <input
                        wire:model.defer="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Masukkan ulang password baru"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-4 flex items-center text-gray-400 transition hover:text-blue-main">

                        <iconify-icon
                            :icon="showPassword ? 'ri:eye-fill' : 'iconoir:eye-closed'"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </button>

                </div>

                @error('password_confirmation')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- ROLE -->
            <div
                x-data="{
                    open: false,
                    selectedIds: @entangle('roleIds').live,
                    roleNames: @js($roles->mapWithKeys(fn ($role) => [$role->id => $role->name])),

                    select(id){
                        id = Number(id)
                        this.selectedIds = this.selectedIds.includes(id)
                            ? this.selectedIds.filter((selectedId) => selectedId !== id)
                            : [...this.selectedIds, id]
                    },

                    label() {
                        const names = this.selectedIds.map((id) => this.roleNames[id]).filter(Boolean)
                        return names.length ? names.join(', ') : 'Pilih role'
                    },

                    toggle(){
                        this.open = !this.open
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Role
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:shield-user-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="label()"
                            class="text-gray-700">
                        </span>

                    </div>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl">

                    <div
                        @click.prevent="selectedIds = []"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Pilih role</span>

                        <iconify-icon
                            x-show="selectedIds.length === 0"
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @foreach ($roles as $role)

                    <div
                        @click.prevent="select({{ $role->id }})"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 capitalize transition hover:bg-blue-main hover:text-white">

                        <span>{{ $role->name }}</span>

                        <iconify-icon
                            x-show="selectedIds.includes({{ $role->id }})"
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @endforeach

                </div>

                @error('roleIds')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- GURU -->
            <div
                x-data="{
                    open: false,
                    selectedId: null,
                    selectedLabel: 'Pilih Guru',

                    init(){
                        this.selectedId = @js($selectedGuru?->id);
                        this.selectedLabel = @js($selectedGuru?->nama ?? 'Pilih Guru');
                    },

                    select(id, label){
                        this.selectedId = id
                        this.selectedLabel = label
                        this.open = false

                        $wire.set('guruId', id) // Update properti Livewire saat memilih guru
                    },

                    toggle(){
                        this.open = !this.open
                    }
                }"
                class="relative">

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Hubungkan ke Guru
                    <span class="font-normal text-gray-400">
                        (Opsional)
                    </span>
                </label>

                <!-- trigger -->
                <div
                    @click="toggle()"
                    class="flex cursor-pointer items-center justify-between rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm transition hover:border-blue-main hover:bg-white">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                            <iconify-icon
                                icon="solar:user-id-bold"
                                width="20"
                                height="20">
                            </iconify-icon>

                        </div>

                        <span
                            x-text="selectedLabel"
                            class="line-clamp-1 text-gray-700">
                        </span>

                    </div>

                    <iconify-icon
                        icon="lineicons:chevron-up"
                        width="20"
                        height="20"
                        class="text-gray-400 transition-transform"
                        :class="{ 'rotate-180': open }">
                    </iconify-icon>

                </div>

                <!-- dropdown -->
                <div
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    style="display:none"
                    class="absolute z-50 mt-2 max-h-52 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl scroll-thin">

                    <div
                        @click.prevent="select(null, 'Pilih guru')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 transition hover:bg-blue-main hover:text-white">

                        <span>Pilih guru</span>

                        <iconify-icon
                            x-show="selectedId === null"
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @foreach ($guru as $selectGuru)

                    <div
                        @click.prevent="select('{{ $selectGuru->id }}', '{{ $selectGuru->nama }}')"
                        class="flex cursor-pointer items-center justify-between px-4 py-3 capitalize transition hover:bg-blue-main hover:text-white">

                        <span>{{ $selectGuru->nama }}</span>

                        <iconify-icon
                            x-show="selectedId === '{{ $selectGuru->id }}'" 
                            icon="lineicons:check"
                            width="18">
                        </iconify-icon>

                    </div>

                    @endforeach

                </div>

                @error('guruId')
                <p class="mt-2 text-sm text-rose-500">
                    {{ $message }}
                </p>
                @enderror

            </div>

            <!-- INFO BOX -->
            <div
                class="rounded-3xl border border-amber-100 bg-amber-50 p-5 md:col-span-2">

                <div class="flex gap-4">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white">

                        <iconify-icon
                            icon="solar:info-circle-bold"
                            width="24"
                            height="24">
                        </iconify-icon>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-800">
                            Catatan Update
                        </h3>

                        <p class="mt-1 text-sm leading-relaxed text-gray-500">
                            Kosongkan password jika tidak ingin mengubah password user. Pastikan role sesuai agar akses menu tetap aman.
                        </p>

                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end md:col-span-2">

                <a href="{{ route('manajemen-user') }}" wire:navigate>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                        Batal

                    </button>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="update"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="update"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="update"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="update">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="update">
                        Menyimpan...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>
