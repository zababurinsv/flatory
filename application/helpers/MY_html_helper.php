<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Script
 *
 * Generates a script inclusion of a JavaScript file
 * Based on the CodeIgniters original Link Tag.
 * 
 * Author(s): Isern Palaus <ipalaus@ipalaus.es>
 * 
 * @access    public
 * @param    mixed    javascript sources or an array
 * @param    string    language
 * @param    string    type
 * @param    boolean    should index_page be added to the javascript path 
 * @return    string
 */
if (!function_exists('script_tag')) {

    function script_tag($src = '', $language = 'javascript', $type = 'text/javascript', $index_page = FALSE) {
        $CI = & get_instance();

        $script = '<script ';

        if (is_array($src)) {
            foreach ($src as $v) {
                if ($k == 'src' AND strpos($v, '://') === FALSE) {
                    if ($index_page === TRUE) {
                        $script .= ' src="' . $CI->config->site_url($v) . '"';
                    } else {
                        $script .= ' src="' . $CI->config->slash_item('base_url') . $v . '"';
                    }
                } else {
                    $script .= "$k=\"$v\"";
                }
            }

            $script .= ">\n";
        } else {
            if (strpos($src, '://') !== FALSE) {
                $script .= ' src="' . $src . '" ';
            } elseif ($index_page === TRUE) {
                $script .= ' src="' . $CI->config->site_url($src) . '" ';
            } else {
                $script .= ' src="' . $CI->config->slash_item('base_url') . $src . '" ';
            }

            $script .= 'language="' . $language . '" type="' . $type . '"';

            $script .= '>' . "\n";
        }


        $script .= '</script>';

        return $script;
    }

}

/**
 * HTML helper
 */
if (!function_exists('html_tag')) {

    /**
     * Create a XHTML tag
     *
     * @param	string			The tag name
     * @param	array|string	The tag attributes
     * @param	string|bool		The content to place in the tag, or false for no closing tag
     * @return	string
     */
    function html_tag($tag, $attr = array(), $content = false) {
        // list of void elements (tags that can not have content)
        static $void_elements = array(
            // html4
            "area", "base", "br", "col", "hr", "img", "input", "link", "meta", "param",
            // html5
            "command", "embed", "keygen", "source", "track", "wbr",
            // html5.1
            "menuitem",
        );

        // construct the HTML
        $html = '<' . $tag;
        $html .= (!empty($attr)) ? ' ' . (is_array($attr) ? array_to_attr($attr) : $attr) : '';

        // a void element?
        if (in_array(strtolower($tag), $void_elements)) {
            // these can not have content
            $html .= ' />';
        } else {
            // add the content and close the tag
            $html .= '>' . $content . '</' . $tag . '>';
        }

        return $html;
    }

}

if (!function_exists('html_data_attr_to_string')) {

    /**
     * Собрать дата атрибуты в виде строки
     * @param array $attr_list - список data атрибутов (ключ - значение, значение может быть функцией в которую передается $content)
     * @param mixed $content - контент который будет передан в аттрибут коллбек
     * @return string
     */
    function html_data_attr_to_string(array $attr_list, $content) {

        $res = '';

        foreach ($attr_list as $key => $val) {
            if (is_callable($val)) {
                $val = $val($content);
            }

            $res .= ' data-' . $key . '="' . $val . '"';
        }
        
        return ltrim($res, ' ');
    }

}