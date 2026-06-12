<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class UploadFileNamer
{
    public static function makePath($file, string $directory, array $parts): string
    {
        $directory = trim($directory, '/');
        $filename = collect($parts)
            ->filter(fn ($part) => filled($part))
            ->map(fn ($part) => Str::slug((string) $part))
            ->filter()
            ->implode('-');

        if ($filename === '') {
            $filename = 'file';
        }

        $filename .= '-' . now()->format('YmdHis');

        return $directory . '/' . $filename . '.' . self::extension($file);
    }

    private static function extension($file): string
    {
        $extension = null;

        if (is_object($file) && method_exists($file, 'getClientOriginalExtension')) {
            $extension = $file->getClientOriginalExtension();
        }

        if (!$extension && is_object($file) && method_exists($file, 'extension')) {
            $extension = $file->extension();
        }

        $extension = Str::lower((string) $extension);

        return preg_match('/^[a-z0-9]+$/', $extension) ? $extension : 'jpg';
    }
}
