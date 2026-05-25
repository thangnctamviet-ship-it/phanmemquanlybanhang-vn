<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_vnd')) {
    function format_vnd($n) {
        if ($n === null || $n === '') return '';
        return number_format((float)$n, 0, ',', '.');
    }
}
