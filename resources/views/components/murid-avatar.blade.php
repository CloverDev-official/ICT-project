@props(['murid'])

@php
    $nama = trim($murid?->nama ?? '');
    $initial = mb_strtoupper(mb_substr($nama, 0, 1, 'UTF-8'), 'UTF-8');
    $foto = trim($murid?->image_path ?? '');
@endphp

<div
    x-data="{}"
    role="img"
    aria-label="{{ $nama }}"
    {{ $attributes->class(['relative flex shrink-0 items-center justify-center overflow-hidden']) }}>
    <span aria-hidden="true">{{ $initial }}</span>
    @if ($foto !== '')
        <img
            src="{{ $foto }}"
            alt=""
            loading="lazy"
            decoding="async"
            x-on:error="$el.style.display = 'none'"
            x-on:load="$el.style.display = 'block'"
            class="absolute inset-0 h-full w-full object-cover">
    @endif
</div>
