<div class="space-y-6">

    <!-- HEADER -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-main via-blue-deep to-[#07162f] p-6 shadow-lg">

        <!-- ornament -->
        <div class="absolute -top-10 -right-10 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 h-36 w-36 rounded-full bg-cyan-300/10 blur-2xl"></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <!-- title -->
            <div class="flex items-center gap-4">

                <div
                    class="hidden h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/10 backdrop-blur sm:flex">

                    <iconify-icon
                        icon="solar:users-group-rounded-bold"
                        width="34"
                        height="34"
                        class="text-white">
                    </iconify-icon>

                </div>

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Manajemen Murid
                    </h1>

                    <p class="mt-1 text-sm text-blue-100">
                        Kelola foto murid berdasarkan data nama dan NIPD.
                    </p>
                </div>

            </div>

            <!-- actions -->
            <div class="grid grid-cols-1 gap-3 sm:flex sm:flex-row">

                <a href="{{ route('tambah-foto') }}" wire:navigate>
                    <button
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-main shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95">

                        <iconify-icon
                            icon="line-md:plus"
                            width="22"
                            height="22"
                            class="transition duration-300 group-hover:rotate-90">
                        </iconify-icon>

                        Tambah Foto Murid

                    </button>
                </a>

            </div>

        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

        <!-- table header -->
        <div
            class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Daftar Foto Murid
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    List murid yang dapat diperbarui foto profilnya.
                </p>
            </div>

            <!-- search -->
            <div class="relative w-full lg:w-80">

                <input
                    type="search"
                    placeholder="Cari nama atau NIPD..."
                    class="w-full rounded-2xl border border-gray-300 bg-white py-3 pl-4 pr-12 text-sm transition placeholder:text-gray-400 hover:border-blue-main focus:border-blue-main focus:outline-none focus:ring-4 focus:ring-blue-100" />

                <div class="absolute inset-y-0 right-4 flex items-center text-gray-400">

                    <iconify-icon
                        icon="mdi:account-search-outline"
                        width="22"
                        height="22">
                    </iconify-icon>

                </div>

            </div>

        </div>

        <!-- table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-5 py-4 text-center font-semibold">
                            No
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Foto Murid
                        </th>

                        <th class="px-5 py-4 text-left font-semibold">
                            Nama
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            NIPD
                        </th>

                        <th class="px-5 py-4 text-center font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ([
                        [
                            'foto' => asset('assets/img/default-avatar.png'),
                            'nama' => 'Deden Agus Rahman',
                            'nipd' => '2025001',
                        ],
                        [
                            'foto' => asset('assets/img/default-avatar.png'),
                            'nama' => 'Muhammad Rizky',
                            'nipd' => '2025002',
                        ],
                        [
                            'foto' => asset('assets/img/default-avatar.png'),
                            'nama' => 'Siti Aisyah',
                            'nipd' => '2025003',
                        ],
                    ] as $murid)

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">

                            <!-- no -->
                            <td class="px-5 py-5 text-center font-medium text-gray-700">
                                {{ $loop->iteration }}
                            </td>

                            <!-- foto -->
                            <td class="px-5 py-5">

                                <div class="flex justify-center">

                                    <div
                                        class="relative h-16 w-16 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 shadow-sm">

                                        <img
                                            src="{{ $murid['foto'] }}"
                                            alt="Foto {{ $murid['nama'] }}"
                                            class="h-full w-full object-cover">

                                    </div>

                                </div>

                            </td>

                            <!-- nama -->
                            <td class="px-5 py-5">

                                <div>
                                    <h3 class="font-semibold capitalize text-gray-800">
                                        {{ $murid['nama'] }}
                                    </h3>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Data foto murid
                                    </p>
                                </div>

                            </td>

                            <!-- nipd -->
                            <td class="px-5 py-5 text-center">

                                <div
                                    class="inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-xs font-semibold text-blue-main">

                                    {{ $murid['nipd'] }}

                                </div>

                            </td>

                            <!-- aksi -->
                            <td class="px-5 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    <!-- lihat -->
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-blue-main transition hover:-translate-y-0.5 hover:bg-blue-main hover:text-white">

                                        <iconify-icon
                                            icon="solar:eye-bold"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                    <!-- edit foto -->
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 transition hover:-translate-y-0.5 hover:bg-amber-500 hover:text-white">

                                        <iconify-icon
                                            icon="solar:gallery-edit-bold"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                    <!-- hapus -->
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 transition hover:-translate-y-0.5 hover:bg-rose-500 hover:text-white">

                                        <iconify-icon
                                            icon="lineicons:trash-3"
                                            width="20"
                                            height="20">
                                        </iconify-icon>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>

        </div>

        <!-- footer -->
        <div
            class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-sm text-gray-500">
                Menampilkan data foto murid.
            </p>

            <div class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-main">
                Total 3 Murid
            </div>

        </div>

    </div>

</div>