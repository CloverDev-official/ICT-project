@assets
<style>
    @media print {

        @page {
            size: 58mm auto;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 58mm;
            background: #fff;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: static;

            width: 58mm;
            min-height: auto;
            margin: 0;
            padding: 3mm;
            box-sizing: border-box;

            box-sizing: border-box;

            background: #fff;
            border: none;
            box-shadow: none;

            font-size: 10px;
            line-height: 1.2;

            page-break-inside: avoid;
            break-inside: avoid;
        }

        #printArea table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        #printArea td {
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        #qrcode {
            width: 26mm !important;
            height: 26mm !important;
            display: block;
            object-fit: contain;
            flex-shrink: 0;
        }

        button {
            display: none !important;
        }

    }
</style>
@endassets

<div class="flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4">

    <button
        type="button"
        onclick="window.print()"
        class="mb-5 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        Print
    </button>

    <div
        id="printArea"
        class="mx-auto w-full max-w-[58mm] rounded bg-white p-3 shadow">

        <div class="w-full">

            <h1 class="mb-3 text-center text-[10px] font-bold uppercase">
                Surat Izin Telat
            </h1>

            <div class="flex flex-col space-y-2">

                <div class="text-[8px]">
                    <p class="font-semibold">Nama :</p>
                    <p>{{ $izin->murid->nama }}</p>
                </div>

                <div class="text-[8px]">
                    <p class="font-semibold">Kelas :</p>
                    <p>{{ $izin->murid->rombel->nama_lengkap ?? 'N/A' }}</p>
                </div>

                <div class="text-[8px]">
                    <p class="font-semibold">NIPD :</p>
                    <p>{{ $izin->murid->nipd }}</p>
                </div>

                <div class="text-[8px]">
                    <p class="font-semibold">Keperluan :</p>
                    <p>{{ $izin->alasan }}</p>
                </div>

            </div>

            <div class="my-4 border border-dotted"></div>

            <!-- Tanda Tangan -->
            <div class="flex justify-end">
                <div class="w-28 text-center text-[8px]">
                    <p class="font-semibold">Pengawas</p>

                    <!-- Area tanda tangan -->
                    <div class="h-14"></div>

                    <div class="border-t border-black pt-1">
                        {{ $izin->pengawas->nama ?? auth()->user()->name }}
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>