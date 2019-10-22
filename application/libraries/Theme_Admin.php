<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Theme_Admin
 * init theme admin
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Theme_Admin {
    
    /**
     *
     * @var \MY_Controller 
     */
    private $_controller;

    public function __construct($params) {
    
        if(!array_get($params, 'controller') instanceof MY_Controller)
            return;
        
        $this->_controller = $params['controller'];
        
        $this->_controller->template_dir = 'admin/';
                
        $this->_controller->load->library('form_validation');
        $this->_controller->load->library('pagination');

        $this->_controller->styles = array(
            'bootstrap.css',
            'simple-sidebar.css',
            'font-awesome.min.css',
            $this->template_dir . 'style.css',
        );

        $this->_controller->scripts = array(
            'jquery-1.8.3.js',
            'jqueryui.custom.js',
            'localization.js',
            'functions.js',
            '/front/forms.js',
        );

        $this->_controller->bottom_scripts = array(
            'bootstrap.js',
            'holder.js',
            'functions.js',
            'dashboard.js',
        );
    }
    
}
