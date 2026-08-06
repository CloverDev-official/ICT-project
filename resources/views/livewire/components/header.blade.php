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
        <div class="hidden md:flex gap-2  justify-center items-center" >
            <div class="flex flex-col gap-0" >
                <h1 class="text-sm font-semibold capitalize" >{{ auth()->user()->name }}</h1>
                <p class="text-xs capitalize" >{{ auth()->user()->role->name }}</p>
            </div>
            <div x-data="{ openModal: false }" >
                <div
                    @click="openModal = !openModal"
                    class="cursor-pointer w-8 h-8 rounded-full bg-white shadow-sm overflow-hidden flex items-center justify-center"
                >
                    @if ($user?->profile_photo_path)
                        <img
                            src="{{ $user->profile_photo_url }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <iconify-icon
                            icon="lineicons:user-4"
                            width="25"
                            height="24"
                            class="text-blue-deep-solid"
                        ></iconify-icon>
                    @endif
                </div>
                <div x-show="openModal" @click.outside="openModal = false" x-transition style="display: none;" class="bg-white p-2 w-40 shadow-md rounded-lg z-50 absolute top-16 right-10 grid grid-cols-1 gap-2">
                    <!-- btn profil -->
                    <a href="{{ route('profil') }}" wire:navigate >
                        <button class="rounded-lg w-full p-2 text-gray-900 flex items-center text-sm transition-all duration-150 hover:bg-gray-200 active:scale-95" >
                                <iconify-icon icon="lineicons:user-4" width="20" height="20"></iconify-icon> Profil
                        </button>
                    </a>
                    <!-- logout -->
                    <button wire:click="logout" class="bg-rose-500 rounded-lg w-full p-2 text-white flex items-center text-sm transition-all duration-150 hover:bg-rose-600 active:scale-95" >
                            <iconify-icon icon="lineicons:exit" width="20" height="20"></iconify-icon> Logout
                    </button>
                </div>
            </div>
        </div>
        <!-- button -->
        <button class="block md:hidden"  @click="openside = !openside">
            <iconify-icon icon="lineicons:menu-hamburger-1" width="32" height="32" class="  hover:opacity-75" ></iconify-icon>
        </button>
    </div>
</div>