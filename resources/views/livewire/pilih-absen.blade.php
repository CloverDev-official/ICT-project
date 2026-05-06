<div>

    <!-- absen murid -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">Absen murid</h1>
            <p class="text-sm text-gray-400">
                Pilih absen murid untuk absen masuk atau absen keluar.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-8">
            <a href="{{ route('scan-qrcode') }}">
                <div class="bg-emerald-500 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 transition-all duration-150 hover:bg-emerald-600 active:scale-95" >
                    <iconify-icon icon="lineicons:enter" width="50" height="50"></iconify-icon>
                    <div>
                        <h1 class="text-2xl font-bold capitalize">absen masuk</h1>
                        <p class="text-gray-100 text-center">06.30 - 08.30</p>
                    </div>
                </div>
            </a>
            <a href="{ route('scan-qrcode') }">
                <div class="bg-amber-500 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                    <iconify-icon icon="lineicons:exit" width="50" height="50"></iconify-icon>
                    <div>
                        <h1 class="text-2xl font-bold capitalize">absen keluar</h1>
                        <p class="text-gray-100 text-center">16.30 - 17.00</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- absen guru -->
    <div class="mt-5 bg-white p-4 rounded-xl shadow-sm">
        <div>
            <h1 class="font-semibold text-gray-800 text-2xl capitalize">Absen guru</h1>
            <p class="text-sm text-gray-400">
                Pilih absen guru untuk absen masuk atau absen keluar.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-8">
            <a href="{{ route('scan-qrcode') }}">
                <div class="bg-emerald-500 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 transition-all duration-150 hover:bg-emerald-600 active:scale-95" >
                    <iconify-icon icon="lineicons:enter" width="50" height="50"></iconify-icon>
                    <div>
                        <h1 class="text-2xl font-bold capitalize">absen masuk</h1>
                        <p class="text-gray-100 text-center">06.30 - 08.30</p>
                    </div>
                </div>
            </a>
            <a href="{ route('scan-qrcode') }">
                <div class="bg-amber-500 text-white p-4 rounded-xl shadow-md flex flex-col items-center justify-center gap-2 transition-all duration-150 hover:bg-amber-600 active:scale-95" >
                    <iconify-icon icon="lineicons:exit" width="50" height="50"></iconify-icon>
                    <div>
                        <h1 class="text-2xl font-bold capitalize">absen keluar</h1>
                        <p class="text-gray-100 text-center">16.30 - 17.00</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>