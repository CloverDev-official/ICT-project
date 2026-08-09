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
        if (app()->environment('local')) {
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
}
