<?php

use Carbon\Carbon;

if (!function_exists('format_date')) {
    /**
     * Định dạng ngày từ Y-m-d sang d-m-Y.
     *
     * @param string|null $date
     * @param string $format
     * @return string|null
     */
    function format_date(?string $date, string $format = 'd-m-Y'): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('parse_date')) {
    /**
     * Chuyển đổi ngày từ d-m-Y sang Y-m-d (để lưu vào DB).
     *
     * @param string|null $date
     * @param string $format
     * @return string|null
     */
    function parse_date(?string $date, string $format = 'd-m-Y'): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::createFromFormat($format, $date)->format('Y-m-d');
    }
}