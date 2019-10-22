<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!function_exists('truncate')) {

    /**
     * truncate string with utf-8 iconv
     * @param string $string - string for truncate
     * @param int $length - max string lenth
     * @param string $truncated_end - end of string after truncate [DEFAULT: '...']
     * @return string
     */
    function truncate($string, $length, $truncated_end = '...') {
        return iconv_strlen($string, 'UTF-8') > (int) $length ? iconv_substr($string, 0, (int) $length, 'UTF-8') . (string) $truncated_end : $string;
    }

}


if (!function_exists('js_file_name')) {

    /**
     * Modify js file name for ENVIRONMENT (.min.js on production)
     * @param string $file_name - name of js file
     * @return string
     */
    function js_file_name($file_name) {
        if (ENVIRONMENT !== 'production')
            return $file_name;

        if (strpos($file_name, '.min.js') !== FALSE)
            return $file_name;
        
        return str_replace('.js', '.min.js', $file_name);
    }

}