<?php


if (!function_exists('sanitize_request_page_size')) {
    function sanitize_request_page_size(mixed $value, int $size = 20) : int
    {
        if(is_numeric($value))
            return $value;

        return $size;
    }
}
