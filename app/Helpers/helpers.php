<?php

use Carbon\Carbon;

if (!function_exists('format_date')) {
    /**
     * Định dạng ngày từ Y-m-d sang d-m-Y, có thể bao gồm cả giờ.
     *
     * @param string|null $date
     * @param string $format
     * @param bool $showTime Hiển thị thêm giờ hay không
     * @return string|null
     */
    function format_date(?string $date, string $format = 'd-m-Y', bool $showTime = false): ?string
    {
        if (!$date) {
            return null;
        }

        if ($showTime) {
            $format .= ' H:i';
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