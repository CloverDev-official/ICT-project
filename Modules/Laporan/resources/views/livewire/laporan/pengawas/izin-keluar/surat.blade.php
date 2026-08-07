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

                <div class="flex flex-col space-y-2" >
                    <!-- nama -->
                    <div class="flex flex-col text-[8px]">
                        <p class="text-start whitespace-nowrap font-semibold" >Nama :</p>
                        <p class="text-start" >{{ $izin->murid->nama }}</p>
                    </div>
                    <!-- kelas -->
                    <div class="flex flex-col text-[8px]">
                        <p class="text-start whitespace-nowrap font-semibold" >Kelas :</p>
                        <p class="text-start">{{ $izin->murid->rombel->nama_lengkap ?? 'N/A' }}</p>
                    </div>
                    <!-- nipd -->
                    <div class="flex flex-col text-[8px]">
                        <p class="text-start whitespace-nowrap font-semibold" >NIPD :</p>
                        <p class="text-start">{{ $izin->murid->nipd }}</p>
                    </div>
                    <!-- keperluan -->
                    <div class="flex flex-col text-[8px]">
                        <p class="text-start whitespace-nowrap font-semibold" >Keperluan :</p>
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
        Livewire.navigate(`/admin/laporan-pengawas/izin`);
    };

});
</script>
@endscript