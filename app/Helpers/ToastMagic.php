<?php

namespace App\Helpers;

use Livewire\Livewire;

class ToastMagic
{
    protected static function dispatch(
        string $status,
        string $title,
        ?string $message,
        array $options = [],
        bool $useSessionFlash = false, // default false
    ): void {
        $component = Livewire::current();

        if ($useSessionFlash) {
            // hanya gunakan session flash jika diaktifkan
            session()->flash('toastMagic', [
                'status' => $status,
                'title' => $title,
                'message' => $message ?? '',
                'options' => $options,
            ]);
        } elseif ($component) {
            $component->dispatch(
                'toastMagic',
                status: $status,
                title: $title,
                message: $message ?? '',
                options: $options,
            );
        }
    }

    public static function success(
        string $title,
        ?string $message = null,
        array $options = [],
        bool $useSessionFlash = false,
    ): void {
        self::dispatch('success', $title, $message, $options, $useSessionFlash);
    }

    public static function warning(
        string $title,
        ?string $message = null,
        array $options = [],
        bool $useSessionFlash = false,
    ): void {
        self::dispatch('warning', $title, $message, $options, $useSessionFlash);
    }

    public static function info(
        string $title,
        ?string $message = null,
        array $options = [],
        bool $useSessionFlash = false,
    ): void {
        self::dispatch('info', $title, $message, $options, $useSessionFlash);
    }

    public static function error(
        string $title,
        ?string $message = null,
        array $options = [],
        bool $useSessionFlash = false,
    ): void {
        self::dispatch('error', $title, $message, $options, $useSessionFlash);
    }
}