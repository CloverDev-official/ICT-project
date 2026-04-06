<div class="flex items-center justify-start mt-2 mb-5">
    <div class="flex flex-col text-start ">
        <h1 class="text-lg md:text-3xl font-bold capitalize">
            {{ str_replace('-', ' ', Route::currentRouteName()) }}
        </h1>
        <h1 class=" md:text-lg font-normal text-gray-600">
            {{ $dateNow ?? 'Tanggal sekarang' }}
        </h1>
    </div>
</div>