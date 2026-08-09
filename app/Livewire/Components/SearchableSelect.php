<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class SearchableSelect extends Component
{
    private const INPUT_SIZES = [
        'sm' => [
            'left' => 'py-2 pl-10 pr-3 text-xs',
            'right' => 'py-2 pl-3 pr-10 text-xs',
        ],
        'md' => [
            'left' => 'py-3 pl-11 pr-4 text-sm',
            'right' => 'py-3 pl-4 pr-11 text-sm',
        ],
        'lg' => [
            'left' => 'py-4 pl-12 pr-5 text-base',
            'right' => 'py-4 pl-5 pr-12 text-base',
        ],
    ];

    private const WIDTHS = [
        'full' => 'w-full',
        'sm' => 'w-48',
        'md' => 'w-64',
        'lg' => 'w-80',
        'xl' => 'w-96',
    ];

    private const ICON_OFFSETS = [
        '1' => ['left' => 'left-1', 'right' => 'right-1'],
        '2' => ['left' => 'left-2', 'right' => 'right-2'],
        '3' => ['left' => 'left-3', 'right' => 'right-3'],
        '4' => ['left' => 'left-4', 'right' => 'right-4'],
        '5' => ['left' => 'left-5', 'right' => 'right-5'],
        '6' => ['left' => 'left-6', 'right' => 'right-6'],
    ];

    private const DROPDOWN_HEIGHTS = [
        'sm' => 'max-h-32',
        'md' => 'max-h-40',
        'lg' => 'max-h-64',
    ];

    #[Modelable]
    public mixed $value = null;

    #[Reactive]
    public mixed $options = [];

    public string $label = '';
    public string $placeholder = '';
    public string $valueKey = '';
    public string $labelKey = '';
    public string $allLabel = 'Semua';
    public string $notFoundText = 'Data tidak ditemukan.';
    public string $inputSize = 'md';
    public string $width = 'full';
    public string $widthClass = '';
    public string $icon = 'mdi:magnify';
    public int|string $iconSize = 20;
    public string $iconPosition = 'right';
    public int|string $iconOffset = '4';
    public string $iconClass = 'text-gray-400';
    public string $labelClass = 'mb-2 block text-sm font-semibold text-gray-700';
    public string $inputClass = '';
    public string $dropdownHeight = 'md';
    public string $dropdownClass = '';
    public string $optionClass = '';
    public string $checkIcon = 'lineicons:check';
    public int|string $checkIconSize = 24;
    public string $checkIconClass = '';

    public function inputSizeClasses(): string
    {
        $size = self::INPUT_SIZES[$this->inputSize] ?? self::INPUT_SIZES['md'];

        return $size[$this->iconPosition()] ?? $size['right'];
    }

    public function widthClasses(): string
    {
        return trim((self::WIDTHS[$this->width] ?? self::WIDTHS['full']) . ' ' . $this->widthClass);
    }

    public function iconPositionClasses(): string
    {
        $offset = self::ICON_OFFSETS[(string) $this->iconOffset] ?? self::ICON_OFFSETS['4'];

        return $offset[$this->iconPosition()];
    }

    public function dropdownHeightClass(): string
    {
        return self::DROPDOWN_HEIGHTS[$this->dropdownHeight] ?? self::DROPDOWN_HEIGHTS['md'];
    }

    public function safeIconSize(): int
    {
        return $this->safeSize($this->iconSize, 20);
    }

    public function safeCheckIconSize(): int
    {
        return $this->safeSize($this->checkIconSize, 24);
    }

    private function iconPosition(): string
    {
        return in_array($this->iconPosition, ['left', 'right'], true)
            ? $this->iconPosition
            : 'right';
    }

    private function safeSize(int|string $size, int $default): int
    {
        $validatedSize = filter_var($size, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 128],
        ]);

        return $validatedSize === false ? $default : $validatedSize;
    }

    public function render()
    {
        return view('livewire.components.searchable-select');
    }
}
