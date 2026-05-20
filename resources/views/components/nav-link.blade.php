@props([
    'href',
    'icon'
])

@php
    $active = request()->url() === $href;

    // mapping icon
    $icons = [
        'dashboard' => [
            'active' => 'mdi:view-dashboard',
            'inactive' => 'mdi:view-dashboard-outline'
        ],
        'pilihAbsen' => [
            'active' => 'mdi:qrcode-scan',
            'inactive' => 'mdi:qrcode'
        ],
        'rekapAbsenMurid' => [
            'active' => 'mdi:account-file-text',
            'inactive' => 'mdi:account-file-text-outline'
        ],
        'rekapAbsenGuru' => [
            'active' => 'mdi:account-file',
            'inactive' => 'mdi:account-file-outline'
        ],

        'riwayatAbsenMurid' => [
            'active' => 'mdi:user-clock',
            'inactive' => 'mdi:user-clock-outline'
        ],
        'rekapAbsenGuru' => [
            'active' => 'mdi:account-file',
            'inactive' => 'mdi:account-file-outline'
        ],
        'absenMurid' => [
            'active' => 'mdi:account-check',
            'inactive' => 'mdi:account-check-outline'
        ],
        'absenGuru' => [
            'active' => 'mdi:account-multiple-check',
            'inactive' => 'mdi:account-multiple-check-outline'
        ],
        'dataMurid' => [
            'active' => 'mdi:user-card-details',
            'inactive' => 'mdi:user-card-details-outline'
        ],
        'dataGuru' => [
            'active' => 'mdi:user-badge',
            'inactive' => 'mdi:user-badge-outline'
        ],
        'dataKelas' => [
            'active' => 'heroicons:user-group-solid',
            'inactive' => 'heroicons:user-group'
        ],
        'dataJurusan' => [
            'active' => 'mdi:academic-cap',
            'inactive' => 'mdi:academic-cap-outline'
        ],
        'manajemenWaktu' => [
            'active' => 'mdi:timer-cog',
            'inactive' => 'mdi:timer-cog-outline'
        ],
        'manajemenQR' => [
            'active' => 'mdi:qrcode-scan',
            'inactive' => 'mdi:qrcode'
        ],
        'manajemenUser' => [
            'active' => 'mdi:account-cog',
            'inactive' => 'mdi:account-cog-outline'
        ],
        'manajemenTahunAjaran' => [
            'active' => 'mdi:account-school',
            'inactive' => 'mdi:account-school-outline'
        ],
        'pengaturan' => [
            'active' => 'mdi:gear',
            'inactive' => 'mdi:gear-outline'
        ],

    ];

    $iconName = $icons[$icon] ?? null;
@endphp

<li>
    <a 
        href="{{ $href }}"
        wire:navigate
        {{ $attributes->merge([
            'class' => 'transition-all duration-150 px-4 py-2 rounded-xl capitalize font-semibold flex items-center gap-2 ' . 
                        ($active 
                            ? 'bg-blue-deep-solid text-white' 
                            : 'hover:bg-blue-deep-solid text-gray-200')
        ]) }}
        
    >
        @if($iconName)
            <iconify-icon 
                icon="{{ $active ? $iconName['active'] : $iconName['inactive'] }}"
                width="24"
                height="24">
            </iconify-icon>
        @endif

        {{ $slot }}
    </a>
</li>