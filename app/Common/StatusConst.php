<?php

namespace App\Common;

class StatusConst
{
    const SEATING_AREA_STATUS = [
        'FREE' => 0,
        'BUSY' => 1,
        'RESERVED' => 2
    ];

    // pending: Chua dat coc, confirmed: da dat coc, preparing: dang chuan bi, arrived: da den, completed: da hoan thanh, canceled: da huy
    const RESERVATION_STATUS = [
        'PENDING' => 0,
        'CONFIRMED' => 1,
        'PREPARING' => 2, // Dang chuan bi do an cho khach: sau khi khach dat do xong thi chuyen ve trang thai nay
        'USING' => 3, // Dang chuan bi do an cho khach: sau khi khach dat do xong thi chuyen ve trang thai nay
        'COMPLETED' => 4,
        'CANCELED' => 5
    ];

    const RESERVATION_STATUS_USING_SEAT = [
        self::RESERVATION_STATUS['PENDING'],
        self::RESERVATION_STATUS['PREPARING'],
        self::RESERVATION_STATUS['CONFIRMED'],
        self::RESERVATION_STATUS['USING'],
    ];

    const RECEIPT_STATUS = [
        'PENDING' => 0,
        'COMPLETED' => 1,
        'CANCELED' => 2
    ];

    const COUPON_STATUS = [
        'ACTIVE' => 1,
        'INACTIVE' => 0
    ];

    const MENU_STATUS = [
        'ACTIVE' => 1,
        'INACTIVE' => 0
    ];

    const USER_STATUS = [
        'TRIAL' => 0,
        'OFFICIAL' => 1,
        'TERMINATION' => 2
    ];
}
