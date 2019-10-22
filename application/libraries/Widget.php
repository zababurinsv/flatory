<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Simple base class for widgets
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Widget {

    /**
     * Super object CodeIgniter
     * @var object 
     */
    protected $_CI;

    /**
     * CI load
     * @var object
     */
    protected $load;

    /**
     * General view path
     * @var string 
     */
    protected $_view_path;

    /**
     * Base controller access
     * @var \MY_Controller
     */
    protected $controller;

    public function __construct($params) {
        $this->_view_path = 'widgets' . DIRECTORY_SEPARATOR;
        $this->_CI = & get_instance();
        $this->load = $this->_CI->load;
        if (isset($params['this']) && is_object($params['this']))
            $this->controller = $params['this'];
    }

}
