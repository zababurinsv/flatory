<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Extended Date helper
 * @date 04.08.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */

if (!function_exists('get_full_date_ru')) {
    /**
     * Get full date ru
     * @param string $date - date("Y-d-m") 
     * @return string
     */
    function get_full_date_ru($date) {
        if(!$date)
            return '';
        
        $trans = array(
            "January" => "января",
            "February" => "февраля",
            "March" => "марта",
            "April" => "апреля",
            "May" => "мая",
            "June" => "июня",
            "July" => "июля",
            "August" => "августа",
            "September" => "сентября",
            "October" => "октября",
            "November" => "ноября",
            "December" => "декабря",
        );

        return date("d", strtotime($date)) . ' ' . strtr(date("F", strtotime($date)), $trans) . ' ' . date("Y", strtotime($date));
    }

}