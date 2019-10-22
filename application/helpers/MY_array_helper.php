<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Extended Array helper
 * @date 26.07.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */

/**
 * Transform array out to tree by key (field)
 * @param array $array - array out to transform
 * @param string $key - field out to root tree
 * @return array (tree)
 */
function simple_tree($array, $key) {
    $tree = array();
    if (empty($array) || !is_array($array))
        return $array;
    foreach ($array as $arr) {
        if (isset($arr[$key]))
            $tree[$arr[$key]] = $arr;
        else
            $tree[] = $arr;
    }

    return $tree;
}

/**
 * Transform array out to tree group by key (field)
 * @param array $array - array out to transform
 * @param string $key - field out to root tree
 * @return array (tree)
 */
function simple_tree_group($array, $key) {
    $tree = array();
    foreach ($array as $arr) {
        $tree[$arr[$key]][] = $arr;
    }
    return $tree;
}

/**
 * Transform array of objects out to tree by key (field)
 * @param array $array - array of objects out to transform
 * @param string $key - field out to root tree
 * @return array - array of objects (tree)
 */
function simple_tree_objects($array, $key) {
    $tree = array();
    foreach ($array as $arr) {
        $tree[$arr->$key] = $arr;
    }
    return $tree;
}

/**
 * var_dump with <pre>
 * @param {type} $var
 * @param int $vdie - if 1 then die()
 * @return type
 */
function vdump($var, $vdie = Null) {
    $name = '';
    $name .= print'<pre>';
//    if (is_array($var))
//        print_r($var);
//    else
//    $name .= var_dump($var);
    $vdie == Null ? die : $name .= print'</pre>';
    return $name;
}

/**
 * Делаем значение элемента массива ключом элемента массива
 * и определяем значение либо значением является первый елемент массива из оставшихся
 * Пример: из 1 получаем 2
 * 1) array(5) {    [0]=> array(3) {["id"]=> string(1) "1", ["Name"]=> string(11) "youtube.com", ["img"]=> NULL}
  [1]=> array(3) {["id"]=> string(1) "2", ["Name"]=> string(11) "deti.ivi.ru", ["img"]=> NULL} }
 * 2) array(2) {[1]=>  string(11) "youtube.com",  [2]=>  string(11) "deti.ivi.ru"}
 * @author Shusharin Valery 
 * @param   array   $array          Список массивов
 * @param   string  $key            Что будет ключем (ключ поля)
 * @param   string  $field_value    Какое поле возьмем в качестве значения (ключ поля)
 * @return  array   Массив
 */
function pluck_key_value($array, $key, $field_value = false) {
    $values = array();
    foreach ($array as $row)
        if (isset($row[$key])) {
            $new_key = $row[$key];
            unset($row[$key]);

            if ($field_value === false) {
                $tmp = array_values($row);
                $values[$new_key] = $tmp[0];
            } else {
                $values[$new_key] = $row[$field_value];
            }
        }
    return $values;
}

function objects_to_arrays($objects) {
    $objects = (array) $objects;
    $result = array();
    foreach ($objects as $item) {
        $result[] = (array) $item;
    }
    return $result;
}

function array_unshift_assoc(&$arr, $key, $val) {
    $arr = array_reverse($arr, true);
    $arr[$key] = $val;
    return array_reverse($arr, true);
}

/**
 * Collect array by key
 * @param array $arr - incoming array
 * @param string $key - key for collect
 * @param bool $is_only_data - only data for this key?
 * @return array
 * 
 * @example // incoming array: 
 * $arr = array(array('key'=> 1),array('key'=> 2),array('key'=> 3));
 * $result = collect_array_by_key($arr, 'key');
 * // output:
 * array(1) {
 * ["key"]=> array(3) {
 *  [0]=> 1
 *  [1]=> 2
 *  [2]=> 3 }
 * }
 */
function collect_array_by_key($arr, $key, $is_only_data = FALSE) {
    $collect = array();
    foreach ($arr as $a) {
        if (isset($a[$key]))
            $collect[$key][] = $a[$key];
    }

    if ($is_only_data === TRUE)
        return isset($collect[$key]) ? $collect[$key] : $collect;

    return $collect;
}

/**
 * get translit string
 * @param string $str - text
 * @param bool $is_strict - is strict mode - lower case and only [a-zA-Z0-9-]
 * @return string
 */
function transliteration($str, $is_strict = TRUE) {
    $trans = array("А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
        "Е" => "E", "Ё" => "YO", "Ж" => "J", "З" => "Z", "И" => "I",
        "Й" => "J", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
        "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
        "У" => "U", "Ф" => "F", "Х" => "KH", "Ц" => "C", "Ч" => "CH",
        "Ш" => "SH", "Щ" => "SHCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
        "Э" => "EH", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo", "ж" => "j",
        "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh",
        "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shch", "ъ" => "y",
        "ы" => "yi", "ь" => "", "э" => "eh", "ю" => "yu", "я" => "ya",
        " " => "-", "-" => "-", "—" => "-", "(" => "", ")" => "", "«" => "",
        "»" => "", "," => "", "%" => "", "." => "", "/" => "", "\'" => "",
        "*" => "", "?" => "", "&" => "", "^" => "", ":" => "", ";" => "", "#" => "",
        "<" => "", ">" => "");
    if ($is_strict)
        $str = mb_strtolower($str);
    $res = str_replace(" ", "_", strtr($str, $trans));
    //если надо, вырезаем все кроме латинских букв, цифр и дефиса (например для формирования логина)
    if ($is_strict)
        $res = preg_replace("|[^a-zA-Z0-9-]|", "", $res);
    return $res;
}

if (!function_exists('array_except')) {

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    function array_except($array, $keys) {
        return array_diff_key($array, array_flip((array) $keys));
    }

}

if (!function_exists('array_get')) {

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_get($array, $key, $default = null) {


        if (is_null($key))
            return $array;

        if (isset($array[$key]))
            return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

}

/**
 * json_encode with JSON_UNESCAPED_UNICODE
 * @see http://www.w-lab.ru/baza-znanii/php-mysql/zamena-json-unescaped-unicode-pri-ispolzovanii-json-encode-v-php-5-3/
 * @param array $arr
 * @return sting
 */
function json_encode_unescaped_unicode($arr) {
    $arrayUtf = array('\u0410', '\u0430', '\u0411', '\u0431', '\u0412', '\u0432', '\u0413', '\u0433', '\u0414', '\u0434', '\u0415', '\u0435', '\u0401', '\u0451', '\u0416', '\u0436', '\u0417', '\u0437', '\u0418', '\u0438', '\u0419', '\u0439', '\u041a', '\u043a', '\u041b', '\u043b', '\u041c', '\u043c', '\u041d', '\u043d', '\u041e', '\u043e', '\u041f', '\u043f', '\u0420', '\u0440', '\u0421', '\u0441', '\u0422', '\u0442', '\u0423', '\u0443', '\u0424', '\u0444', '\u0425', '\u0445', '\u0426', '\u0446', '\u0427', '\u0447', '\u0428', '\u0448', '\u0429', '\u0449', '\u042a', '\u044a', '\u042b', '\u044b', '\u042c', '\u044c', '\u042d', '\u044d', '\u042e', '\u044e', '\u042f', '\u044f');
    $arrayCyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш', 'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я');
    return str_replace($arrayUtf, $arrayCyr, json_encode($arr));
}

/**
 * Element - strict mod of element()
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is not exist it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('element_strict')) {

    function element_strict($item, $array, $default = FALSE) {
        if (!isset($array[$item])) {
            return $default;
        }

        return $array[$item];
    }

}

/**
 * Get config - return $config
 * @param string $filename
 * @return boolean / array
 */
if (!function_exists('load_config')) {

    function load_config($filename) {
        $path_config = APPPATH . 'config' . DIRECTORY_SEPARATOR;
        if (ENVIRONMENT !== 'production')
            $path_config .= ENVIRONMENT . DIRECTORY_SEPARATOR;
        $ext = '.php';
        if (!file_exists($path_config . $filename . $ext))
            return FALSE;
        $config_load = require ($path_config . $filename . $ext);
        return isset($config) ? $config : array();
    }

}

if (!function_exists('treeze')) {

    /**
     * tree
     * @param array $a
     * @param string $parent_key
     * @param string $children_key
     */
    function treeze(&$a, $parent_key, $children_key) {
        $orphans = true;
        $i;
        while ($orphans) {
            $orphans = false;

            foreach ($a as $k => $v) {
                // is there $a[$k] sons?
                $sons = false;
                foreach ($a as $x => $y)
                    if (element($parent_key, $y, false) !== false and $y[$parent_key] == $k) {
                        $sons = true;
                        $orphans = true;
                        break;
                    }
                // $a[$k] is a son, without children, so i can move it
                if (!$sons && element($parent_key, $v, false) !== false) {
                    $a[$v[$parent_key]][$children_key][$k] = $v;
                    unset($a[$k]);
                }
            }
        }
        return $a;
    }

}

if (!function_exists('implode_int')) {

    /**
     * Implode to int values
     * @param string $glue - Defaults to an empty string.
     * @param array $pieces - The array of strings to implode.
     * @return string
     */
    function implode_int($glue, array $pieces) {
        // for php 5.2
        if (!function_exists('_implode_int_callback')) {

            function _implode_int_callback(&$value) {
                $value = (int) $value;
            }

        }
        array_walk($pieces, _implode_int_callback);
        return implode($glue, $pieces);
    }

}

if (!function_exists('http_build_query_with_arrays')) {

    /**
     * http_build_query + fix arrays
     * @see http://stackoverflow.com/questions/17161114/php-http-build-query-with-two-array-keys-that-are-same
     * @link http://php.net/manual/en/function.http-build-query.php
     * @param string $query_data
     * @param string $numeric_prefix
     * @param string $arg_separator
     * @param int $enc_type
     * @return string a URL-encoded string.
     */
    function http_build_query_with_arrays($query_data, $numeric_prefix = null, $arg_separator = null, $enc_type = 'PHP_QUERY_RFC1738') {
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '[]=', http_build_query($query_data, $numeric_prefix = null, $arg_separator = null, $enc_type = 'PHP_QUERY_RFC1738'));
    }

}

if (!function_exists('array_to_attr')) {

    /**
     * Takes an array of attributes and turns it into a string for an html tag
     *
     * @param	array	$attr
     * @return	string
     */
    function array_to_attr($attr) {
        $attr_str = '';

        foreach ((array) $attr as $property => $value) {
            // Ignore null/false
            if ($value === null or $value === false) {
                continue;
            }

            // If the key is numeric then it must be something like selected="selected"
            if (is_numeric($property)) {
                $property = $value;
            }

            $attr_str .= $property . '="' . str_replace('"', '&quot;', $value) . '" ';
        }

        // We strip off the last space for return
        return trim($attr_str);
    }

}

if (!function_exists('implode_by_field')) {

    /**
     * Ext implode. Implod by field (assoc arrays)
     * @param string $glue <p>
     * Defaults to an empty string.
     * </p> 
     * @param array $pieces <p>
     * The array of strings to implode.
     * </p>
     * @param string $field <p>
     * The field name.
     * </p>
     * @return string
     * @see http://php.net/manual/en/function.implode.php
     * @throws Exception
     */
    function implode_by_field($glue, array $pieces, $field) {

        if (!is_string($field))
            throw new Exception('$field must be as string, ' . gettype($pieces) . ' given!');

        $res = [];

        foreach ($pieces as $it) {

            if (!is_array($it) || !isset($it[$field]) || !is_string($it[$field]))
                continue;

            $res[] = $it[$field];
        }

        return implode($glue, $res);
    }

}

if (!function_exists('array_push_unique')) {

    /**
     * Add in array value if not exist
     * @link http://php.net/manual/en/function.array-push.php
     * @link http://php.net/manual/en/function.array-search.php
     * @param array $array - array for add
     * @param mixed $value - value for add in array
     * @return int - int the new number of elements in the array
     */
    function array_push_unique(array &$array, $value) {

        if (!in_array($value, $array))
            return array_push($array, $value);

        return array_search($value, $array);
    }

}