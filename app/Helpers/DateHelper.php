<?php

namespace App\Helpers;

use App\Common\DateConst;
use Carbon\Carbon;

class DateHelper
{
    /**
     * @param $date
     *
     * @return string
     */
    public static function parseDateToString($date): string
    {
        return Carbon::parse($date)->toDateTimeString();
    }

    /**
     * @param          $date
     * @param string $format
     *
     * @return string
     */
    public static function parseDateToServerDate($date, string $format = 'Y-m-d'): string
    {
        return Carbon::parse($date)->format($format);
    }

    public static function now($format = 'Y-m-d H:i:s'): string
    {
        $now = Carbon::now();

        return $now->format($format);
    }

    public static function timestamp(): int
    {
        return Carbon::now()->getTimestamp();
    }

    // $diff = $date2 - $date1, default $absolute = true
    public static function diffDatetimeWithUnit($date1, $date2, string $unit = DateConst::DAY, bool $absolute = true): int
    {
        if (!is_a($date1, Carbon::class)) {
            $date1 = Carbon::make($date1);
        }

        if (!is_a($date2, Carbon::class)) {
            $date2 = Carbon::make($date2);
        }
        return match ($unit) {
            DateConst::SECOND => $date1->diffInSeconds($date2, $absolute),
            DateConst::MINUTE => $date1->diffInMinutes($date2, $absolute),
            DateConst::HOUR => $date1->diffInHours($date2, $absolute),
            default => $date1->diffInDays($date2, $absolute),
        };
    }

    // Check date is weekend
    public static function isWeekend(string $date): bool
    {
        return Carbon::parse($date)->isWeekend();
    }

    // Count week between two date
    public static function weekBetween2Date($date1, $date2): int
    {
        return Carbon::parse($date2)->weekOfYear - Carbon::parse($date1)->weekOfYear;
    }

    public static function totalWeekend($date1, $date2): int
    {
        $totalWeekend = 0;
        $startDate = Carbon::parse($date1);
        $endDate = Carbon::parse($date2);
        while ($startDate <= $endDate) {
            if ($startDate->dayOfWeek === Carbon::SATURDAY || $startDate->dayOfWeek === Carbon::SUNDAY) {
                $totalWeekend++;
            }
            $startDate->addDay();
        }
        return $totalWeekend;
    }

    public static function countAttendanceHours($date1, $date2, $time1, $time2): float|int
    {
        $totalDays = DateHelper::diffDatetimeWithUnit($date1, $date2) - DateHelper::totalWeekend($date1, $date2);
        if ($totalDays == 0 && DateHelper::isWeekend($date1) && DateHelper::isWeekend($date2)) return 0;

        if ($totalDays == 0 && DateHelper::isWeekend($date1)) {
            $totalHours = DateHelper::diffDatetimeWithUnit('08:00:00', $time2, DateConst::MINUTE);
        } else if ($totalDays == 0 && DateHelper::isWeekend($date2)) {
            $totalHours = DateHelper::diffDatetimeWithUnit($time1, '17:00:00', DateConst::MINUTE);
            if ($time1 <= '12:00:00') $totalHours -= 60;
        } else if ($totalDays == 0 && !DateHelper::isWeekend($date1) && !DateHelper::isWeekend($date2)) {
            $totalHours = DateHelper::diffDatetimeWithUnit($time1, $time2, DateConst::MINUTE);
        } else if ($totalDays != 0 && DateHelper::isWeekend($date1) && DateHelper::isWeekend($date2)) {
            $totalHours = ($totalDays + 1) * 8 * 60;
        } else if ($totalDays != 0 && DateHelper::isWeekend($date1)) {
            $totalHours = DateHelper::diffDatetimeWithUnit('08:00:00', $time2, DateConst::MINUTE) + $totalDays * 8 * 60;
        } else if ($totalDays != 0 && DateHelper::isWeekend($date2)) {
            $totalHours = DateHelper::diffDatetimeWithUnit($time1, '17:00:00', DateConst::MINUTE) + $totalDays * 8 * 60;
            if ($time1 <= '12:00:00') $totalHours -= 60;
        } else {
            $totalHours = DateHelper::diffDatetimeWithUnit($time1, '17:00:00', DateConst::MINUTE) + DateHelper::diffDatetimeWithUnit('08:00:00', $time2, DateConst::MINUTE) + ($totalDays - 1) * 8 * 60;
            if ($time1 <= '12:00:00') $totalHours -= 60;
        }
        return $totalHours / 60;
    }
}
