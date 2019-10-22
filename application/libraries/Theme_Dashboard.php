<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Theme_Dashboard
 * init theme dashboard
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Theme_Dashboard {

    /**
     *
     * @var \MY_Controller 
     */
    private $_controller;

    public function __construct($params) {

        if (!array_get($params, 'controller') instanceof MY_Controller)
            return;

        $this->_controller = $params['controller'];

        $this->_controller->template_dir = 'dashboard/';

        $this->_controller->load->library('form_validation');
        $this->_controller->load->library('pagination', ['controller' => $this->_controller]);

        $this->_controller->header = $this->_controller->load->view($this->_controller->template_dir . 'header', [], TRUE);

        $this
                ->_controller
                ->set_scripts([
                    '/vendor/jquery/dist/jquery.min.js',
                    '/vendor/jquery-ui/jquery-ui.min.js',
                    '/vendor/slimScroll/jquery.slimscroll.min.js',
                    '/vendor/bootstrap/dist/js/bootstrap.min.js',
                    '/vendor/jquery-flot/jquery.flot.js',
                    '/vendor/jquery-flot/jquery.flot.resize.js',
                    '/vendor/jquery-flot/jquery.flot.pie.js',
                    '/vendor/flot.curvedlines/curvedLines.js',
                    '/vendor/jquery.flot.spline/index.js',
                    '/vendor/metisMenu/dist/metisMenu.min.js',
                    '/vendor/iCheck/icheck.min.js',
                    '/vendor/peity/jquery.peity.min.js',
                    '/vendor/sparkline/index.js',
                    '/vendor/doT/doT.min.js',
                    '/vendor/select2/dist/js/select2.min.js',
                    '/js/tag-it.min.js',
                    '/js/moment.min.js',
                    '/js/daterangepicker.js',
                    '/js/fl/fl.js',
                    '/js/jquery_ext.js',
                ])
                ->set_styles([
                    '/vendor/fontawesome/css/font-awesome.css',
                    '/vendor/metisMenu/dist/metisMenu.css',
                    '/vendor/animate.css/animate.css',
                    '/vendor/bootstrap/dist/css/bootstrap.css',
                    '/vendor/select2/dist/css/select2.min.css',
                    '/css/jquery-ui.min.css',
                    '/css/jquery.tagit.css',
                    '/css/daterangepicker-bs3.css',
                    '/css/' . $this->_controller->template_dir . 'style.css',
                    '/css/' . $this->_controller->template_dir . 'main.css',
                ])
                ->set_scripts_bottom([
                    '/js/' . $this->_controller->template_dir . 'homer.js',
//                    '/js/holder.js',
//                    '/js/functions.js',
                    '/js/dashboard.js',
                    '/js/fl/fl_filter.js',
        ]);
    }

    /**
     * Render content page
     * @param string $content
     * @return string
     */
    public function render_page($content) {
        return $this->_controller->load->view($this->_controller->template_dir . 'page', [
                    'content' => $content,
                    'title' => $this->_controller->title,
                    'breadcrumbs' => $this->_controller->get_breadcrumb(),
                        ], TRUE);
    }

}
