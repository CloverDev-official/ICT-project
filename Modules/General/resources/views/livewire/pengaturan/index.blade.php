<div class="mb-20 space-y-6">

    <!-- HERO -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- ornament -->
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-5">

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur">

                    <iconify-icon
                        icon="solar:settings-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Pengaturan Website
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola identitas website, logo, gambar login, dan copyright aplikasi.
                    </p>
                </div>

            </div>

            <!-- badge -->
            <div
                class="rounded-2xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">

                <p class="text-xs uppercase tracking-[0.2em] text-blue-100">
                    Sistem
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    App Setting
                </h2>

            </div>

        </div>
    </div>

    @include('components.mobile-fullscreen')

    <!-- FORM CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- CARD HEADER -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Identitas Website
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Data ini akan tampil pada halaman login, sidebar, dan footer aplikasi.
                </p>
            </div>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">

                <iconify-icon
                    icon="solar:shield-check-bold"
                    width="18"
                    height="18">
                </iconify-icon>

                Data Aman

            </div>

        </div>

        <!-- FORM -->
        <form
            wire:submit.prevent="save"
            class="space-y-8 p-6">

            <!-- SECTION TEXT -->
            <div>

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                        <iconify-icon
                            icon="solar:document-text-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Informasi Website
                        </h3>

                        <p class="text-sm text-gray-500">
                            Atur nama website dan teks copyright.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- NAMA WEBSITE -->
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Nama Website
                        </label>

                        <div class="relative">

                            <input
                                type="text"
                                wire:model.defer="namaWebsite"
                                placeholder="Contoh : ICT Absensi"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                            <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <iconify-icon
                                    icon="solar:global-bold"
                                    width="22"
                                    height="22">
                                </iconify-icon>
                            </div>

                        </div>

                        @error('namaWebsite')
                            <p class="mt-2 text-sm text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <!-- COPYRIGHT -->
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Copyright
                        </label>

                        <div class="relative">

                            <input
                                type="text"
                                wire:model.defer="copyright"
                                placeholder="Contoh : SMKN 2 Banjarmasin © 2026"
                                class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100" />

                            <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <iconify-icon
                                    icon="solar:copyright-bold"
                                    width="22"
                                    height="22">
                                </iconify-icon>
                            </div>

                        </div>

                        @error('copyright')
                            <p class="mt-2 text-sm text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>

            <!-- SECTION MEDIA -->
            <div class="border-t border-gray-200 pt-8">

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">

                        <iconify-icon
                            icon="solar:gallery-bold"
                            width="22"
                            height="22">
                        </iconify-icon>

                    </div>

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Media Website
                        </h3>

                        <p class="text-sm text-gray-500">
                            Upload logo dan gambar utama untuk halaman login.
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <!-- LOGO -->
                    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                        <!-- preview -->
                        <div
                            class="flex min-h-64 items-center justify-center bg-gradient-to-br from-blue-50 to-white p-6">

                            <div
                                class="flex h-36 w-36 items-center justify-center overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-sm">

                                <img
                                    src="{{ $logoSource }}"
                                    class="h-full w-full object-contain"
                                    alt="Preview Logo">

                            </div>

                        </div>

                        <!-- input -->
                        <div class="border-t border-gray-200 p-5">

                            <div class="mb-4">

                                <h4 class="font-bold text-gray-800">
                                    Logo Website
                                </h4>

                                <p class="mt-1 text-sm text-gray-500">
                                    Logo akan tampil pada sidebar, login, dan header.
                                </p>

                            </div>

                            <label
                                class="group flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-dashed border-blue-300 bg-blue-50 px-5 py-4 text-sm font-semibold text-blue-main transition hover:bg-blue-main hover:text-white">

                                <iconify-icon
                                    icon="solar:upload-bold"
                                    width="20"
                                    height="20"
                                    class="transition group-hover:-translate-y-0.5">
                                </iconify-icon>

                                Upload Logo

                                <input
                                    type="file"
                                    wire:model="logo"
                                    accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml"
                                    class="hidden">

                            </label>

                            <div
                                wire:loading.flex
                                wire:target="logo"
                                class="mt-3 items-center gap-2 rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-main">
                                <iconify-icon icon="line-md:loading-twotone-loop" width="18" height="18"></iconify-icon>
                                Mengupload logo...
                            </div>

                            @error('logo')
                                <p class="mt-2 text-sm text-rose-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    <!-- GAMBAR LOGIN -->
                    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                        <!-- preview -->
                        <div
                            class="relative min-h-64 overflow-hidden bg-gray-100">

                            <img
                                src="{{ $loginImageSource }}"
                                class="h-64 w-full object-cover"
                                alt="Preview Gambar Login">

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-blue-deep/80 via-blue-deep/20 to-transparent">
                            </div>

                            <div class="absolute bottom-5 left-5 right-5 text-white">

                                <div
                                    class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold backdrop-blur">

                                    <iconify-icon
                                        icon="solar:login-2-bold"
                                        width="18"
                                        height="18">
                                    </iconify-icon>

                                    Login Banner

                                </div>

                                <h4 class="text-xl font-bold">
                                    Gambar Halaman Login
                                </h4>

                                <p class="mt-1 text-sm text-blue-100">
                                    Preview gambar yang akan tampil di sisi kanan halaman login.
                                </p>

                            </div>

                        </div>

                        <!-- input -->
                        <div class="border-t border-gray-200 p-5">

                            <div class="mb-4">

                                <h4 class="font-bold text-gray-800">
                                    Gambar Login
                                </h4>

                                <p class="mt-1 text-sm text-gray-500">
                                    Gunakan gambar landscape agar tampilan login lebih rapi.
                                </p>

                            </div>

                            <label
                                class="group flex cursor-pointer items-center justify-center gap-2 rounded-2xl border border-dashed border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-600 hover:text-white">

                                <iconify-icon
                                    icon="solar:gallery-add-bold"
                                    width="20"
                                    height="20"
                                    class="transition group-hover:-translate-y-0.5">
                                </iconify-icon>

                                Upload Gambar Login

                                <input
                                    type="file"
                                    wire:model="loginImage"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                    class="hidden">

                            </label>

                            <div
                                wire:loading.flex
                                wire:target="loginImage"
                                class="mt-3 items-center gap-2 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-600">
                                <iconify-icon icon="line-md:loading-twotone-loop" width="18" height="18"></iconify-icon>
                                Mengupload gambar login...
                            </div>

                            @error('loginImage')
                                <p class="mt-2 text-sm text-rose-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            <!-- PREVIEW WEBSITE -->
            <div class="border-t border-gray-200 pt-8">

                <div
                    class="overflow-hidden rounded-3xl border border-gray-200 bg-gray-50">

                    <div class="border-b border-gray-200 bg-white px-6 py-5">

                        <h3 class="text-lg font-bold text-gray-800">
                            Preview Tampilan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Contoh tampilan identitas website setelah disimpan.
                        </p>

                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

                        <!-- preview brand -->
                        <div
                            class="rounded-3xl border border-gray-200 bg-white p-5">

                            <div class="flex items-center gap-4">

                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 p-3">

                                    <img
                                        src="{{ $logoSource ?? asset('assets/img/logo_smkn_2.png') }}"
                                        class="h-full w-full object-contain"
                                        alt="Logo Preview">

                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-800">
                                        {{ $namaWebsite ?? 'ICT Absensi' }}
                                    </h4>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $copyright ?? 'SMKN 2 Banjarmasin © 2026' }}
                                    </p>
                                </div>

                            </div>

                        </div>

                        <!-- preview copyright -->
                        <div
                            class="flex items-center justify-between gap-4 rounded-3xl border border-gray-200 bg-white p-5">

                            <div>
                                <p class="text-sm text-gray-400">
                                    Footer Copyright
                                </p>

                                <h4 class="mt-1 font-semibold text-gray-800">
                                    {{ $copyright ?? 'SMKN 2 Banjarmasin © 2026' }}
                                </h4>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-main">

                                <iconify-icon
                                    icon="solar:copyright-bold"
                                    width="24"
                                    height="24">
                                </iconify-icon>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    wire:click="resetForm"
                    class="w-full rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-auto">

                    Reset

                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save,logo,loginImage"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">

                    <iconify-icon
                        wire:loading.remove
                        wire:target="save,logo,loginImage"
                        icon="lineicons:save"
                        width="20"
                        height="20"
                        class="transition group-hover:scale-110">
                    </iconify-icon>

                    <iconify-icon
                        wire:loading
                        wire:target="save,logo,loginImage"
                        icon="line-md:loading-twotone-loop"
                        width="20"
                        height="20">
                    </iconify-icon>

                    <span wire:loading.remove wire:target="save,logo,loginImage">
                        Simpan Pengaturan
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>

                    <span wire:loading wire:target="logo,loginImage">
                        Mengupload...
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>
