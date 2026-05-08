<?php

namespace App\Support;

class DateHelper
{
    public static function generateCalendar($month, $year)
    {
        $firstDay = strtotime("$year-$month-01");

        $daysInMonth = date('t', $firstDay);

        $startDay = date('N', $firstDay);

        $calendar = [];

        $day = 1;

        for ($i = 1; $i <= 42; $i++) {

            if ($i < $startDay || $day > $daysInMonth) {
                $calendar[] = null;
            } else {

                $date = "$year-" .
                    str_pad($month, 2, '0', STR_PAD_LEFT) .
                    "-" .
                    str_pad($day, 2, '0', STR_PAD_LEFT);

                $calendar[] = [
                    'day' => $day,
                    'date' => $date
                ];

                $day++;
            }
        }

        return $calendar;
    }

    public static function getDefaultSchedule($date)
    {
        $dayName = date('N', strtotime($date));

        /*
        |--------------------------------------------------------------------------
        | JUMAT
        |--------------------------------------------------------------------------
        */

        if ($dayName == 5) {

            return [
                'masuk' => '06:30',
                'pulang' => '11:30',
                'type' => 'khusus'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SABTU & MINGGU
        |--------------------------------------------------------------------------
        */

        if ($dayName >= 6) {

            return [
                'masuk' => null,
                'pulang' => null,
                'type' => 'libur'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SENIN - KAMIS
        |--------------------------------------------------------------------------
        */

        return [
            'masuk' => '06:30',
            'pulang' => '16:30',
            'type' => 'normal'
        ];
    }
}