<div
    x-show="openModal"
    x-transition
    style="display: none;"
    class="fixed inset-0 flex items-center justify-center bg-black/30 p-4">

    <div
        @click.outside="openModal = false"
        class="bg-white rounded-xl shadow-lg w-[420px] p-6">

        <h2 class="text-lg flex items-center justify-center gap-1 font-semibold mb-4">
            <iconify-icon icon="lineicons:pencil-1" width="24" height="24"></iconify-icon>
            Edit Kehadiran
        </h2>

        <!-- FORM -->
        <div class="flex flex-col gap-4">

            <!-- Status Kehadiran -->
            <div>
                <label class="text-sm font-medium text-gray-700">
                    Kehadiran
                </label>

                <select
                    class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                    focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpha">Tanpa Keterangan</option>

                </select>
            </div>

            <!-- Jam Masuk -->
            <div>
                <label class="text-sm font-medium text-gray-700">
                    Jam Masuk
                </label>

                <input
                    type="time"
                    class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                    focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Jam Pulang -->
            <div>
                <label class="text-sm font-medium text-gray-700">
                    Jam Pulang
                </label>

                <input
                    type="time"
                    class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                    focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="text-sm font-medium text-gray-700">
                    Keterangan
                </label>

                <textarea
                    rows="3"
                    placeholder="Tambahkan keterangan..."
                    class="w-full mt-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                    focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
            </div>

        </div>


        <!-- BUTTON -->
        <div class="flex justify-end gap-3 mt-6">

            <button
                @click="openModal = false"
                class="px-4 py-2 text-sm bg-gray-300 rounded-lg hover:bg-gray-400">
                Tutup
            </button>

            <button
                class="px-4 py-2 text-sm bg-blue-main text-white rounded-lg hover:bg-blue-deep-solid">
                Simpan
            </button>

        </div>
    </div>
</div>