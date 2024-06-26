<?php

namespace App\Helpers;

use App\Models\Coupon;
use App\Models\Schedule;

class Helper
{
    public static function get_seat_number($schedule_id, $seat_number)
    {
        $schedule = Schedule::find($schedule_id);
        $seat_number = $schedule->seat_numbers()->where('seat_number', $seat_number)->first();
        return $seat_number;
    }

    public static function generate_coupon_code()
    {
        $code = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }
        return $code;
    }

    public static function generate_order_code()
    {
        $code = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }
        return $code;
    }

    public static function percentageChange($part, $whole)
    {
        if ($whole == 0) {
            return 0; // Untuk menghindari pembagian oleh nol
        }

        return ($part / $whole) * 100;
    }

    public static function diffPercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return 0; // Untuk menghindari pembagian oleh nol
        }

        $diff = $current - $previous;

        return ($diff / $previous) * 100;
    }
}
