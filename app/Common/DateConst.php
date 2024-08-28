<?php

namespace App\Common;

class DateConst
{
    const DATE_FORMAT_MYSQL = '%Y-%m-%d';
    const DATE_FORMAT_SER = 'Y-m-d';
    const DATE_FORMAT_CLIENT = 'yyyy-mm-dd';
    const DATE_FORMAT_RANGE = 'YYYY-MM-DD';
    const SECOND = 'second';
    const MINUTE = 'minute';
    const HOUR = 'hour';
    const DAY = 'day';
    const SUNDAY = 1;
    const MONDAY = 2;
    const TUESDAY = 3;
    const WEDNESDAY = 4;
    const THURSDAY = 5;
    const FRIDAY = 6;
    const SATURDAY = 7;
    const WEEKDAY = [self::SUNDAY, self::MONDAY, self::TUESDAY, self::WEDNESDAY, self::THURSDAY, self::FRIDAY, self::SATURDAY];
}
