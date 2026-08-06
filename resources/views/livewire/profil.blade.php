<div class="mb-20 space-y-6">

    <!-- PROFILE SUMMARY -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- USER CARD -->
        <div
            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm lg:col-span-1">

            <div class="border-b border-gray-200 bg-gray-50 px-6 py-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Informasi Profil
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Detail akun yang sedang digunakan.
                </p>

            </div>

            <div class="p-6">

                <div class="flex flex-col items-center text-center">

                    <div
                        class="relative h-32 w-32 overflow-hidden rounded-[2rem] border border-gray-200 bg-gray-100 shadow-sm">

                        <img
                            src="{{ $profilePhotoPreview ?: ($user->profile_photo_url ?? asset('assets/img/default-avatar.png')) }}"
                            class="h-full w-full object-cover"
                            alt="Foto Profil">

                    </div>

                    <h3 class="mt-5 text-2xl font-bold capitalize text-gray-800">
                        {{ $user->name ?? 'Nama Pengguna' }}
                    </h3>

                    <div
                        class="mt-3 inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                        <iconify-icon
                            icon="solar:user-id-bold"
                            width="18"
                            height="18">
                        </iconify-icon>

                        {{ $roleName ?? 'Role Pengguna' }}

                    </div>

                </div>

                <div class="mt-6 space-y-3">

                    <div
                        class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 p-4">

                        <div>
                            <p class="text-xs text-gray-400">
                                Nama Pengguna
                            </p>

                            <h4 class="mt-1 font-semibold capitalize text-gray-800">
                                {{ $user->name ?? 'Nama Pengguna' }}
                            </h4>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                            <iconify-icon
                                icon="solar:user-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                    </div>

                    <div
                        class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 p-4">

                        <div>
                            <p class="text-xs text-gray-400">
                                Role Pengguna
                            </p>

                            <h4 class="mt-1 font-semibold capitalize text-gray-800">
                                {{ $roleName ?? 'Role Pengguna' }}
                            </h4>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">

                            <iconify-icon
                                icon="solar:shield-check-bold"
                                width="22"
                                height="22">
                            </iconify-icon>

                        </div>

                    </div>
                    <!-- logout -->
                    <button wire:click="logout" class="bg-rose-500 rounded-lg w-full p-2 text-white flex justify-center items-center text-sm transition-all duration-150 hover:bg-rose-600 active:scale-95" >
                            <iconify-icon icon="lineicons:exit" width="20" height="20"></iconify-icon> Logout
                    </button>
                </div>

            </div>

        </div>

        <!-- SETTINGS -->
        <div class="space-y-6 lg:col-span-2">

            <!-- EDIT FOTO PROFIL -->
            <div
                x-data="{
                    preview: null,

                    setPreview(event) {
                        const file = event.target.files[0]

                        if (!file) {
                            return
                        }

                        this.preview = URL.createObjectURL(file)
                    }
                }"
                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <div
                    class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Edit Foto Profil
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Upload foto profil baru untuk akun kamu.
                        </p>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                        <iconify-icon
                            icon="solar:gallery-bold"
                            width="18"
                            height="18">
                        </iconify-icon>

                        Foto Profil

                    </div>

                </div>

                <form
                    wire:submit.prevent="updateProfilePhoto"
                    class="p-6">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-[12rem_1fr]">

                        <!-- preview -->
                        <div
                            class="flex items-center justify-center rounded-3xl border border-dashed border-blue-300 bg-blue-50 p-5">

                            <div
                                class="h-36 w-36 overflow-hidden rounded-[2rem] border border-white bg-white shadow-sm">

                                <img
                                    x-show="preview"
                                    :src="preview"
                                    class="h-full w-full object-cover"
                                    alt="Preview Foto Profil">

                                <img
                                    x-show="!preview"
                                    src="{{ $profilePhotoPreview ?: ($user->profile_photo_url ?? asset('assets/img/default-avatar.png')) }}"
                                    class="h-full w-full object-cover"
                                    alt="Foto Profil Saat Ini">

                            </div>

                        </div>

                        <!-- upload -->
                        <div class="flex flex-col justify-center">

                            <label
                                class="group flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-dashed border-blue-300 bg-blue-50 px-5 py-5 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                                <iconify-icon
                                    icon="solar:upload-bold"
                                    width="20"
                                    height="20"
                                    class="transition group-hover:-translate-y-0.5">
                                </iconify-icon>

                                Pilih Foto Profil

                                <input
                                    x-ref="photoInput"
                                    type="file"
                                    wire:model="photo"
                                    accept="image/*"
                                    class="hidden"
                                    @change="setPreview($event)">

                            </label>

                            <p class="mt-3 text-sm text-gray-500">
                                Gunakan foto berbentuk persegi agar hasil preview lebih rapi.
                            </p>

                            @error('photo')
                                <p class="mt-2 text-sm text-rose-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    <div
                        class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            wire:click="resetProfilePhoto"
                            @click="preview = null; if ($refs.photoInput) { $refs.photoInput.value = null }"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                            Reset

                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updateProfilePhoto,photo"
                            class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                            <iconify-icon
                                wire:loading.remove
                                wire:target="updateProfilePhoto,photo"
                                icon="lineicons:save"
                                width="20"
                                height="20"
                                class="transition group-hover:scale-110">
                            </iconify-icon>

                            <iconify-icon
                                wire:loading
                                wire:target="updateProfilePhoto,photo"
                                icon="line-md:loading-twotone-loop"
                                width="20"
                                height="20">
                            </iconify-icon>

                            <span wire:loading.remove wire:target="updateProfilePhoto,photo">
                                Simpan Foto
                            </span>

                            <span wire:loading wire:target="updateProfilePhoto,photo">
                                Menyimpan...
                            </span>

                        </button>

                    </div>

                </form>

            </div>

            <!-- GANTI PASSWORD -->
            <div
                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <div
                    class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Ganti Password
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Perbarui password akun untuk menjaga keamanan akses.
                        </p>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-600">

                        <iconify-icon
                            icon="solar:lock-password-bold"
                            width="18"
                            height="18">
                        </iconify-icon>

                        Keamanan

                    </div>

                </div>

                <form
                    wire:submit.prevent="updatePassword"
                    class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                    <!-- password lama -->
                    <div
                        x-data="{ showPassword: false }"
                        class="md:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Password Lama
                        </label>

                        <div class="relative">

                            <input
                                wire:model.defer="current_password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Masukkan password lama"
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

                        @error('current_password')
                            <p class="mt-2 text-sm text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <!-- password baru -->
                    <div x-data="{ showPassword: false }">

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Password Baru
                        </label>

                        <div class="relative">

                            <input
                                wire:model.defer="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Masukkan password baru"
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

                    <!-- konfirmasi password -->
                    <div x-data="{ showPassword: false }">

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Konfirmasi Password
                        </label>

                        <div class="relative">

                            <input
                                wire:model.defer="password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Ulangi password baru"
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

                    <!-- info -->
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
                                    Catatan Keamanan
                                </h3>

                                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                    Gunakan password yang kuat dan jangan bagikan password kepada orang lain.
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- action -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end md:col-span-2">

                        <button
                            type="button"
                            wire:click="resetPasswordForm"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                            Batal

                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                            <iconify-icon
                                wire:loading.remove
                                wire:target="updatePassword"
                                icon="solar:lock-password-bold"
                                width="20"
                                height="20"
                                class="transition group-hover:scale-110">
                            </iconify-icon>

                            <iconify-icon
                                wire:loading
                                wire:target="updatePassword"
                                icon="line-md:loading-twotone-loop"
                                width="20"
                                height="20">
                            </iconify-icon>

                            <span wire:loading.remove wire:target="updatePassword">
                                Simpan Password
                            </span>

                            <span wire:loading wire:target="updatePassword">
                                Menyimpan...
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>