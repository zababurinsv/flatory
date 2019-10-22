<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Flpagination {

    private $_CI;
    private $_view_path = 'libraries/';
    private $_per_page = 15;
    private $_total_page;
    private $_point = 'page';

    public function __construct() {
        $this->_CI = & get_instance();
    }

    /**
     * Generate pagination
     * @param type $params
     * @return type
     */
    public function pagination($params) {

        $get = xss_clean($_GET);
        
        // current page 
        $current = (int) element($this->_point, $get, 1);
        // limit per page
        $this->_per_page = (int) element('per_page', $params, $this->_per_page);
        // total elements
        $total_rows = (int) element('total_rows', $params, 0);
        $this->_total_page = ceil($total_rows / $this->_per_page);
        // uri
        $base_url = element('base_url', $params, '/') . '?' . http_build_query(array_except($get, $this->_point));
        // prev ∕ next index
        $prev = $current - 1 > 0 ? $current - 1 : FALSE;
        $next = $this->_total_page >= $current + 1 ? $current + 1 : FALSE;

        // view name
        $view = element('view', $params, 'flpagination');
        // no pagination
        if ($total_rows < $this->_per_page)
            return '';

        return $this->_CI->load->view($this->_view_path . $view, array(
                    'current' => $current,
                    'per_page' => $this->_per_page,
                    'total' => $this->_total_page,
                    'base_url' => $base_url . '&' . $this->_point . '=',
                    'prev' => $prev,
                    'next' => $next,
                    'point' => $this->_point,
                        ), TRUE);
    }

    /**
     * Get offset
     * @param int $page
     * @param int $limit
     * @return int
     */
    public function get_offset($page = FALSE, $limit = FALSE) {
        $limit = !$limit ? $this->_per_page : $limit;
        $page = !$page ? (int) element($this->_point, $_GET, 0) - 1 : (int) $page - 1;
        $page = $page < 0 ? $page = 0 : $page;
        return $page * $limit;
    }

    /**
     * Get limit
     * @return int
     */
    public function get_limit() {
        return $this->_per_page;
    }

    /**
     * Set limit
     * @param int $limit
     */
    public function set_limit($limit) {
        $this->_per_page = (int) $limit;
    }

}
