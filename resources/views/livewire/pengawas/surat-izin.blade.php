@assets
<style>
@media print {

    @page {
        size: auto;
        margin: 0;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
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

        width: 72mm;
        max-width: 100%;

        margin: 0 auto;
        padding: 4mm;

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
        class="mx-auto w-full max-w-sm rounded bg-white p-3 shadow">

        <div class="flex items-start gap-3">

            <!-- QR -->
            <div class="flex shrink-0 items-center justify-center">
                <img
                    id="qrcode"
                    class="h-[98px] w-[98px] object-contain"
                    alt="QR Code">
            </div>

            <!-- Informasi -->
            <div class="min-w-0 flex-1 overflow-hidden">

                <h1 class="mb-2 text-center text-sm font-bold uppercase leading-none">
                    Surat Izin Keluar
                </h1>

                <table class="w-full table-fixed text-[11px] leading-tight">
                    <tbody>

                        <tr>
                            <td class="w-[55px] whitespace-nowrap font-semibold align-top">
                                Nama
                            </td>
                            <td class="w-3 text-center align-top">
                                :
                            </td>
                            <td class="whitespace-normal break-all align-top">
                                {{ $izin->murid->nama }}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-[55px] whitespace-nowrap pt-1 font-semibold align-top">
                                NIPD
                            </td>
                            <td class="pt-1 text-center align-top">
                                :
                            </td>
                            <td class="pt-1 whitespace-normal break-all align-top">
                                {{ $izin->murid->nipd }}
                            </td>
                        </tr>

                        <tr>
                            <td class="w-[55px] whitespace-nowrap pt-1 font-semibold align-top">
                                Keperluan
                            </td>
                            <td class="pt-1 text-center align-top">
                                :
                            </td>
                            <td class="pt-1 whitespace-normal break-all align-top">
                                {{ $izin->alasan }}
                            </td>
                        </tr>

                    </tbody>
                </table>

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
        Livewire.navigate(`/admin/laporan-pengawas/izin`);
    };

});
</script>
@endscript