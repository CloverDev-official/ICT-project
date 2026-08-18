@assets
<style>
@media print {

    @page {
        size: 58mm auto;
        margin: 0;
    }

    html,
    body {
        width: 58mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    body {
        display: block !important;
    }

    body * {
        visibility: hidden;
    }

    #printArea,
    #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;

        width: 58mm !important;
        min-width: 58mm !important;
        max-width: 58mm !important;

        margin: 0 !important;
        padding: 3mm !important;

        box-sizing: border-box !important;

        background: #fff !important;
        border: none !important;
        box-shadow: none !important;

        font-size: 10px;
        line-height: 1.2;

        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* Container utama di dalam printArea */
    #printArea > div {
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 3mm !important;
    }

    /* QR */
    #qrcode {
        width: 26mm !important;
        height: 26mm !important;

        display: block !important;

        margin-left: auto !important;
        margin-right: auto !important;

        object-fit: contain;
    }

    /* Informasi */
    #printArea > div > div:last-child {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;

        margin-left: auto !important;
        margin-right: auto !important;

        flex: none !important;
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

    <div class="flex flex-col justify-center items-center gap-3">

        <!-- QR -->
        <div class="flex shrink-0 items-center justify-center">
            <img
                id="qrcode"
                class="h-[98px] w-[98px] object-contain"
                alt="QR Code">
        </div>

        <!-- Informasi -->
        <div class="min-w-0 w-[100px] max-w-[100px] flex-1 overflow-hidden">

            <div class="border border-dotted w-full mb-4"></div>

            <h1 class="mb-2 text-center text-[10px] font-bold uppercase leading-none">
                Surat Izin Keluar
            </h1>

            <div class="flex flex-col space-y-2">

                <div class="flex flex-col text-[8px]">
                    <p class="text-start whitespace-nowrap font-semibold">Nama :</p>
                    <p class="text-start">{{ $izin->murid->nama }}</p>
                </div>

                <div class="flex flex-col text-[8px]">
                    <p class="text-start whitespace-nowrap font-semibold">Kelas :</p>
                    <p class="text-start">
                        {{ $izin->murid->rombel->nama_lengkap ?? 'N/A' }}
                    </p>
                </div>

                <div class="flex flex-col text-[8px]">
                    <p class="text-start whitespace-nowrap font-semibold">NIPD :</p>
                    <p class="text-start">{{ $izin->murid->nipd }}</p>
                </div>

                <div class="flex flex-col text-[8px]">
                    <p class="text-start whitespace-nowrap font-semibold">Keperluan :</p>
                    <p class="text-start">{{ $izin->alasan }}</p>
                </div>

            </div>
        </div>

    </div>
</div>

</div>

@script
<script type="module">
Promise.all([
    import('{{ Vite::asset("resources/js/generateQR.js") }}'),
]).then(async () => {

    const qrCode = @json($qrCode);

    const bytes = await generateQRPNG(qrCode);

    const blob = new Blob([bytes], {
        type: 'image/png'
    });

    const url = URL.createObjectURL(blob);

    const img = document.getElementById('qrcode');

    img.src = url;

    img.onload = () => {
        URL.revokeObjectURL(url);

        window.print();
        Livewire.navigate('{{ route('laporan-izin-keluar') }}');
    };

});
</script>
@endscript