<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('big_ru_money_format')) {

    /**
     * format numbers for big ru money
     * @param number $num - num
     * @param bool $is_skip_label - default label [DEFAULT: false]
     * @return string
     */
    function big_ru_money_format($num, $is_skip_label = FALSE) {
        
        if ($num > 1000000) {
            $num = str_replace('.', ',', round($num / 1000000, 2)) . (!!$is_skip_label ? '' : ' млн р.');
        } elseif ($num > 1000) {
            $num = str_replace('.', ',',round($num / 1000, 2)) . (!!$is_skip_label ? '' : ' тыс. р.');
        } else {
            $num = number_format($num, 0, ',', ' ');
        }
        return $num;
    }

}