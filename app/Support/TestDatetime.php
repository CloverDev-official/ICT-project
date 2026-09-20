<?php

namespace App\Support;

use App\Models\Setting;
use Carbon\Carbon;

class TestDatetime
{
    public const SETTING_KEY = 'system.test_datetime';

    public static function forScheduler(): ?Carbon
    {
        if (! app()->environment('local')) {
            return null;
        }

        $value = Setting::valueOf(self::SETTING_KEY);

        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
