<div>
    <div class="flex items-center justify-start gap-5">
        <!-- btn tambah data siswa -->
        <a 
            href="{{ route('tambah-murid') }}"
            wire:navigate>
            <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white transition-all duration-200 hover:bg-emerald-700 active:scale-95 flex items-center justify-center gap-1 capitalize " >
                <iconify-icon icon="line-md:plus" width="20" height="20"></iconify-icon>
                tambah data murid
            </button>
        </a>

        <!-- btn import CSV -->
        <a href="" wire:navigate>
            <button class="px-4 py-2 rounded-lg bg-blue-main text-white transition-all duration-200 hover:bg-blue-deep-solid active:scale-95 flex items-center justify-center gap-1 capitalize " >
                <iconify-icon icon="line-md:file-import" width="20" height="20"></iconify-icon>
                import CSV
            </button>
        </a> 
    </div>
</div>
