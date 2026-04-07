<?php

namespace App\Helpers;

use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

class ValidateMagic
{
    /**
     * Jalankan validasi tanpa harus nulis $this di komponen Livewire.
     *
     * @param array $rules Aturan validasi
     * @param array $messages Pesan error custom
     * @param string $toastType Jenis toast: error|warning|info|success
     * @return array|null
     */
    public static function run(array $rules, array $messages = [], string $toastType = 'warning')
    {
        // Ambil komponen Livewire aktif
        $component = Livewire::current();

        if (!$component) {
            throw new \Exception('ValidateMagic hanya bisa dipakai di dalam komponen Livewire.');
        }

        try {
            return $component->validate($rules, $messages);
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $error) {
                match ($toastType) {
                    'error' => ToastMagic::error($error),
                    'info' => ToastMagic::info($error),
                    'success' => ToastMagic::success($error),
                    default => ToastMagic::warning($error),
                };
            }
            return null;
        }
    }
}