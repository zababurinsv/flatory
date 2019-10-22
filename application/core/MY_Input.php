<?php

/**
 * MY_Input
 *
 * @date 02/06/2016
 */
class MY_Input extends CI_Input {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Fetch an item from the GET array
     *
     * @access	public
     * @param	string
     * @param	bool
     * @return	string
     */
    public function get($index = NULL, $xss_clean = FALSE) {
        $get = parent::get($index, $xss_clean);
        return ($index === NULL && !is_array($get)) ? [] : $get;
    }

    /**
     * Fetch an item from the POST array
     *
     * @access	public
     * @param	string
     * @param	bool
     * @return	string
     */
    public function post($index = NULL, $xss_clean = FALSE) {
        $post = parent::post($index, $xss_clean);
        return ($index === NULL && !is_array($post)) ? [] : $post;
    }

}
