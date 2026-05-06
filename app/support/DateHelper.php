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

        // Jumat
        if ($dayName == 5) {
            return [
                'masuk' => '07:00',
                'pulang' => '11:00',
                'type' => 'jumat'
            ];
        }

        // Sabtu & Minggu
        if ($dayName >= 6) {
            return [
                'masuk' => '-',
                'pulang' => '-',
                'type' => 'libur'
            ];
        }

        // Hari biasa
        return [
            'masuk' => '07:00',
            'pulang' => '15:00',
            'type' => 'normal'
        ];
    }
}