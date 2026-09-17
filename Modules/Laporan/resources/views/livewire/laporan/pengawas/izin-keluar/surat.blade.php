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
        width: 50mm !important;
        height: 50mm !important;

        display: block !important;

        margin-left: auto !important;
        margin-right: auto !important;

        object-fit: contain;
    }

    button {
        display: none !important;
    }
}
</style>
@endassets

<div class="flex h-fit flex-col items-center justify-center bg-gray-100 p-4">

    <button
        type="button"
        onclick="window.print()"
        class="mb-5 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        Print
    </button>

    <div
        id="printArea"
        class="w-[58mm] rounded bg-white p-3 shadow">
        <div class="flex w-full flex-col items-center gap-3">
            <h1 class="text-center text-lg font-bold uppercase">
                Surat Izin Keluar
            </h1>
            <img
                id="qrcode"
                class="object-contain"
                alt="QR Code Surat Izin Keluar">
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
