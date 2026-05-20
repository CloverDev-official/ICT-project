<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-blue-deep via-blue-main to-blue-light p-4">

    <!-- background ornaments -->
    <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl"></div>
    <div class="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-950/20 blur-3xl"></div>

    <div class="relative z-10 flex min-h-screen items-center justify-center">

        <!-- CARD -->
        <div
            class="w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/20 bg-white/95 shadow-2xl backdrop-blur">

            <div class="grid grid-cols-1 lg:grid-cols-2">

                <!-- FORM SECTION -->
                <div class="flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-14">

                    <!-- logo & title -->
                    <div class="mb-10 text-center">

                        <div
                            class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 shadow-sm">

                            <img
                                src="{{ asset('assets/img/logo_smkn_2.png') }}"
                                class="w-14"
                                alt="Logo SMKN 2 Banjarmasin">

                        </div>

                        <h1 class="text-3xl font-bold capitalize text-blue-deep font-heading">
                            Selamat Datang
                        </h1>

                        <p class="mt-2 text-sm text-gray-500">
                            Masuk ke sistem ICT Absensi SMKN 2 Banjarmasin
                        </p>

                    </div>

                    <!-- form -->
                    <form
                        wire:submit.prevent="login"
                        class="space-y-5">

                        @csrf

                        <!-- email -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Nama atau Email
                            </label>

                            <div class="relative">

                                <input
                                    type="text"
                                    wire:model="email"
                                    placeholder="Masukkan nama atau email"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">

                                <div
                                    class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                                    <iconify-icon
                                        icon="lineicons:user-4"
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

                        <!-- password -->
                        <div x-data="{ showPassword: false }">

                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Password
                            </label>

                            <div class="relative">

                                <input
                                    wire:model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Masukkan password"
                                    class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100">

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

                        <!-- submit -->
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="login"
                            class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-main to-blue-deep px-5 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70">

                            <iconify-icon
                                wire:loading.remove
                                wire:target="login"
                                icon="solar:login-2-bold"
                                width="20"
                                height="20"
                                class="transition group-hover:translate-x-0.5">
                            </iconify-icon>

                            <iconify-icon
                                wire:loading
                                wire:target="login"
                                icon="line-md:loading-twotone-loop"
                                width="20"
                                height="20">
                            </iconify-icon>

                            <span wire:loading.remove wire:target="login">
                                Masuk
                            </span>

                            <span wire:loading wire:target="login">
                                Memproses...
                            </span>

                        </button>

                    </form>

                    <!-- footer -->
                    <div class="mt-8 text-center">

                        <p class="text-xs text-gray-400">
                            © 2026 SMKN 2 Banjarmasin. All rights reserved.
                        </p>

                    </div>

                </div>

                <!-- IMAGE SECTION -->
                <div class="relative hidden min-h-[560px] overflow-hidden lg:block">

                    <img
                        src="{{ asset('assets/img/skenda-profil.jpeg') }}"
                        alt="SMKN 2 Banjarmasin"
                        class="h-full w-full object-cover">

                    <!-- overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-blue-deep/90 via-blue-deep/30 to-transparent">
                    </div>

                    <!-- content -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">

                        <div
                            class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold backdrop-blur">

                            <iconify-icon
                                icon="solar:shield-check-bold"
                                width="18"
                                height="18">
                            </iconify-icon>

                            Sistem Absensi Digital

                        </div>

                        <h2 class="text-3xl font-bold leading-tight">
                            ICT Absensi
                            <br>
                            SMKN 2 Banjarmasin
                        </h2>

                        <p class="mt-3 max-w-md text-sm leading-relaxed text-blue-100">
                            Kelola absensi murid dan guru dengan lebih cepat, rapi, dan terintegrasi.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>