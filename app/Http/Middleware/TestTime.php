<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TestTime
{
    public function handle(Request $request, Closure $next): Response
    {
        // Livewire writes a temporary upload before `_finishUpload` runs. Its
        // cleanup uses `now()`, so a simulated future date would immediately
        // classify that new file as older than a day and remove it.
        if (app()->environment('local') && ! $this->isLivewireTemporaryUpload($request)) {
            $testDatetime = $request->cookie('test_datetime');

            if ($testDatetime) {
                Carbon::setTestNow(Carbon::parse($testDatetime));
            }
        }

        try {
            return $next($request);
        } finally {
            // Session cookies must use the actual current time when Laravel saves them.
            Carbon::setTestNow();
        }
    }

    private function isLivewireTemporaryUpload(Request $request): bool
    {
        if ($request->routeIs('livewire.upload-file')) {
            return true;
        }

        if (! $request->routeIs('default-livewire.update')) {
            return false;
        }

        foreach ($request->input('components', []) as $component) {
            foreach ($component['calls'] ?? [] as $call) {
                if (($call['method'] ?? null) === '_finishUpload') {
                    return true;
                }
            }
        }

        return false;
    }
}
