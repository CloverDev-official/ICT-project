@php
    $selectOptions = collect($options)
        ->map(function ($option, $key) use ($valueKey, $labelKey) {
            // Array/object dengan value + label dari key tertentu
            if (is_array($option) || is_object($option)) {
                return [
                    'value' => data_get($option, $valueKey),
                    'label' => (string) data_get($option, $labelKey, ''),
                ];
            }

            // Array sederhana: ['Hadir', 'Izin', 'Sakit']
            return [
                'value' => $key,
                'label' => (string) $option,
            ];
        })
        ->filter(fn (array $option) => $option['label'] !== '')
        ->values();

    $selectedOption = $selectOptions->first(
        fn (array $option) =>
            $value !== null &&
            (string) $option['value'] === (string) $value
    );
@endphp

<div
    x-data="{
        open: false,
        query: @js($selectedOption['label'] ?? ($value === null ? $allLabel : '')),
        selectedId: $wire.entangle('value').live,
        selectedLabel: @js($selectedOption['label'] ?? ($value === null ? $allLabel : null)),

        init() {
            this.$watch('selectedId', () => this.syncSelectedOption())
        },

        currentOptions() {
            return JSON.parse(this.$root.dataset.options)
        },

        syncSelectedOption() {
            const option = this.currentOptions().find((item) => String(item.value) === String(this.selectedId))

            this.selectedLabel = this.selectedId === null
                ? this.$root.dataset.allLabel
                : (option?.label ?? null)

            if (! this.open) {
                this.query = this.selectedLabel ?? ''
            }
        },

        select(id, label) {
            this.selectedId = id
            this.selectedLabel = label
            this.query = label
            this.open = false
        },

        close() {
            this.open = false
            this.query = this.selectedLabel ?? this.$root.dataset.allLabel
        }
    }"
    data-options='@json($selectOptions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)'
    data-all-label="{{ $allLabel }}"
    @click.outside="close()"
    class="{{ $this->widthClasses() }} relative">

    <label class="{{ $labelClass }}">
        {{ $label }}
    </label>

    <div class="relative">
        <input
            x-model="query"
            @focus="open = true; query = ''"
            @input="open = true"
            @keydown.escape="close()"
            type="text"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-2xl border border-gray-300 bg-gray-50 text-gray-700 transition hover:border-blue-main hover:bg-white focus:border-blue-main focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100 {{ $this->inputSizeClasses() }} {{ $inputClass }}">

        <iconify-icon
            icon="{{ $icon }}"
            width="{{ $this->safeIconSize() }}"
            height="{{ $this->safeIconSize() }}"
            class="absolute top-1/2 -translate-y-1/2 {{ $this->iconPositionClasses() }} {{ $iconClass }}">
        </iconify-icon>
    </div>

    <div
        x-show="open"
        x-transition
        style="display:none"
        class="absolute z-50 mt-2 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg scroll-thin {{ $this->dropdownHeightClass() }} {{ $dropdownClass }}">

        <div
            @click.prevent="select(null, @js($allLabel))"
            x-show="!query || @js(strtolower($allLabel)).includes(query.toLowerCase())"
            class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white {{ $optionClass }}">
            <span>{{ $allLabel }}</span>

            <iconify-icon
                x-show="selectedId === null"
                icon="{{ $checkIcon }}"
                width="{{ $this->safeCheckIconSize() }}"
                height="{{ $this->safeCheckIconSize() }}"
                class="{{ $checkIconClass }}">
            </iconify-icon>
        </div>

        @foreach ($selectOptions as $option)
            <div
                @click.prevent="select(@js($option['value']), @js($option['label']))"
                x-show="!query || @js(strtolower($option['label'])).includes(query.toLowerCase())"
                class="flex cursor-pointer items-center justify-between px-4 py-2 transition hover:bg-blue-deep-solid hover:text-white {{ $optionClass }}">
                <span>{{ $option['label'] }}</span>

                <iconify-icon
                    x-show="String(selectedId) === @js((string) $option['value'])"
                    icon="{{ $checkIcon }}"
                    width="{{ $this->safeCheckIconSize() }}"
                    height="{{ $this->safeCheckIconSize() }}"
                    class="{{ $checkIconClass }}">
                </iconify-icon>
            </div>
        @endforeach

        <p
            x-show="query && !currentOptions().some((item) => item.label.toLowerCase().includes(query.toLowerCase())) && !@js(strtolower($allLabel)).includes(query.toLowerCase())"
            class="px-4 py-3 text-sm text-gray-500">
            {{ $notFoundText }}
        </p>
    </div>
</div>
