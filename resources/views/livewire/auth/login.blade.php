<div  class="bg-linear-to-t/hsl from-blue-deep  to-blue-light">
    <div class="min-h-screen flex items-center justify-center">
        <div data-aos="fade-up" data-aos-duration="800" class="bg-white p-4 rounded-4xl shadow-lg drop-shadow-2xl md:w-md lg:w-3xl">
            <div class="flex justify-between">
                <!-- form -->
                <div class="flex flex-col items-center justify-center flex-1 p-10">
                    
                    <!-- text -->
                    <div class="flex flex-col items-center justify-center gap-4 " >
                        <img src="{{ asset('assets/img/logo_smkn_2.png') }}" class="w-14" alt="">
                        <div class="text-center" >
                            <h1 class="text-2xl font-bold text-blue-deep capitalize font-heading">selamat datang di ICT</h1>
                            <p class="text-xs text-blue-dark" >Silahkan isi nama dan password Anda</p>
                        </div>
                    </div>

                    <!-- input form -->
                    <form action="" class="mt-10 flex flex-col gap-4">
                        <div class="relative" >
                            <input type="text" placeholder="Nama atau Email" class="rounded-full px-4 pr-10 py-2 border-2 border-gray-200 focus:border-blue-main focus:outline-hidden text-sm">
                            <iconify-icon icon="lineicons:user-4" width="24" height="24" class=" text-gray-400 absolute right-3 top-2" ></iconify-icon>
                        </div>
                        <div class="relative" x-data="{open : true}" >
                            <input type="password" :type=" open ? 'password' : 'text' " placeholder="Password" class=" rounded-full px-4 pr-10 py-2 border-2 border-gray-200 focus:border-blue-main focus:outline-hidden text-sm" >
                            <div class="absolute right-3 top-2 ">
                                <button type="button"  @click="open = !open" >
                                    <iconify-icon :icon="open ? 'iconoir:eye-closed' : 'ri:eye-fill' " width="24" height="24" class="transition-transform duration-200 text-gray-400" ></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <input type="submit" value="Masuk" class=" bg-blue-main py-2 px-4 rounded-full text-white transition-all duration-200 hover:bg-blue-secondary hover:scale-105 active:scale-95" >
                    </form>
                </div>
                <!-- banner -->
                <div class="hidden md:flex flex-1" >
                    <img src="{{ asset('assets/img/skenda-profil.jpeg')}}" alt="" class="rounded-4xl shadow" >
                </div>
            </div>
        </div>
    </div>
</div>
