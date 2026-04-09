<div class="flex items-start justify-between mt-2 mb-5">
    <div class="flex flex-col text-start ">
        <h1 class="text-lg md:text-3xl font-bold capitalize">
            {{ str_replace('-', ' ', Route::currentRouteName()) }}
        </h1>
        <h1 class=" md:text-lg font-normal text-gray-600">
            {{ $dateNow ?? 'Tanggal sekarang' }}
        </h1>
    </div>
    <div class="flex items-center justify-end gap-5" >
        <a href="" wire:navigate>
            <button class="bg-blue-deep-solid w-8 h-8  rounded-lg text-white flex items-center justify-center transition-all duration-150 hover:bg-blue-deep hover:text-gray-400 active:scale-95">
                <iconify-icon icon="la:qrcode" width="20" height="20"></iconify-icon>
            </button>
        </a>
        <div class="hidden md:flex gap-2  justify-center items-center" >
            <div class="flex flex-col gap-0" >
                <h1 class="text-sm font-semibold capitalize" >nama user</h1>
                <p class="text-xs capitalize" >role user</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-white shadow-sm text-blue-deep-solid  flex items-center justify-center ">
                <iconify-icon icon="lineicons:user-4" width="25" height="24"></iconify-icon>
            </div>
        </div>
        <!-- button -->
        <button class="block md:hidden"  @click="openside = !openside">
            <iconify-icon icon="lineicons:menu-hamburger-1" width="32" height="32" class="  hover:opacity-75" ></iconify-icon>
        </button>
    </div>
</div>