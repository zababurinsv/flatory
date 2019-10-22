<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Front controller for admin
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Controller extends CI_Controller {

    public $template_dir = '';
    public $title = '';
    public $header = '';
    public $content = '';
    public $footer = '';
    public $after_body = '';
    public $styles = array();
    public $scripts = array();
    public $bottom_scripts = array();
    private $html_tpls = array();
    private $breadcrumbs = [];

    /**
     *
     * @var object 
     */
    private $_user;

    /**
     *
     * @var array 
     */
    private $_user_settings;

    /**
     *
     * @var \Theme_Dashboard 
     */
    private $_theme;

    /**
     * Form validation
     * @var \MY_Form_validation 
     */
    public $form_validation;

    /**
     * 
     * @var string 
     */
    private $force_current_menu_path = '';

    //put your code here
    public function __construct() {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['login_ok'])) {
            redirect('/admin/login');
        }

        $this->load->library('session');
        $this->_user = $this->session->userdata('user');


//        vdump($this->_user);
        // load current theme
        $this->load->library('Theme_Dashboard', ['controller' => $this]);
        $this->_theme = $this->theme_dashboard;

        $this->title = 'Панель управления';
        $this->set_breadcrumb($this->title, '/admin/');

        if (!$this->template_dir)
            throw new Exception('Template not init!');
    }

    /**
     * Render page
     * @param string $content - view
     */
    public function render($content = Null) {

//        $this->load->model('M_handbk');

//        var_dump($this->content);die;
//        var_dump('ddddd');die;
        $data = array(
            'title' => $this->title,
            'header' => $this->header,
            'footer' => $this->footer,
            'styles' => $this->styles,
            'scripts' => $this->scripts,
            'bottom_scripts' => $this->bottom_scripts,
            'content' => !!$content ? $content : $this->_theme->render_page($this->content),
            'after_body' => $this->after_body,
            'html_tpls' => $this->html_tpls,
            'handbks' => $this->M_handbk->handbks,
            'breadcrumbs' => $this->breadcrumbs,
            'menu' => $this->_render_main_menu(),
        );


        $this->load->view($this->template_dir . 'layout', $data);
    }

    /**
     * Set scripts
     * @param array/string $scrpts
     * @return \MY_Controller
     */
    public function set_scripts($scripts) {
        if (is_array($scripts)) {
            foreach ($scripts as $script)
                if (!in_array($script, $this->scripts))
                    $this->scripts[] = $script;
        }else {
            if (!in_array($scripts, $this->scripts))
                $this->scripts[] = $scripts;
        }
        return $this;
    }

    /**
     * Set scripts
     * @param array/string $scrpts
     * @return \MY_Controller
     */
    public function set_scripts_bottom($scripts) {
        if (is_array($scripts)) {
            foreach ($scripts as $script)
                if (!in_array($script, $this->bottom_scripts))
                    $this->bottom_scripts[] = $script;
        }else {
            if (!in_array($scripts, $this->bottom_scripts))
                $this->bottom_scripts[] = $scripts;
        }
        return $this;
    }

    /**
     * Set styles
     * @param array/string $scrpts
     * @return \MY_Controller
     */
    public function set_styles($scripts) {
        if (is_array($scripts)) {
            foreach ($scripts as $script)
                if (!in_array($script, $this->styles))
                    $this->styles[] = $script;
        }else {
            if (!in_array($scripts, $this->styles))
                $this->styles[] = $scripts;
        }
        return $this;
    }

    /**
     * Set html_tpls
     * @param array/string $tpl
     * @return \MY_Controller
     */
    public function set_html_tpls($tpl) {
        if (is_array($tpl)) {
            foreach ($tpl as $t)
                if (!in_array($t, $this->html_tpls))
                    $this->html_tpls[] = $t;
        }else {
            if (!in_array($tpl, $this->html_tpls))
                $this->html_tpls[] = $tpl;
        }
        return $this;
    }

    /**
     * set breadcrumb
     * @param string $title - title
     * @param string $url - url
     * @return \MY_Controller
     */
    public function set_breadcrumb($title, $url = FALSE) {
        $this->breadcrumbs[] = ['title' => $title, 'url' => !!$url ? $url : ''];
        return $this;
    }

    /**
     * render main menu
     * @todo get menu from db
     * @return string (view)
     */
    private function _render_main_menu() {
        return $this->load->view($this->template_dir . 'navs/main_menu', [
                    'force_current_path' => $this->force_current_menu_path
                        ], TRUE);
    }

    public function get_breadcrumb() {
        return $this->breadcrumbs;
    }

    /**
     * Get user settings
     * @todo session.user_settings ???
     * @param string $section
     * @return array | mixed 
     */
    public function get_user_settings($section = NULL) {

        if (!$this->_user || !$this->_user->user_id)
            return [];

        $this->_user_settings = is_array($this->_user_settings) ? $this->_user_settings : json_decode($this->db->where('user_id', $this->_user->user_id)->get('user_settings')->row()->settings, TRUE);

//        vdump($this->_user);
//        vdump($this->user->user_id);
//        vdump($this->db->where('user_id', $this->user->user_id)->get('user_settings')->row()->settings);
//        vdump($this->_user_settings);

        return !!$section ? array_get($this->_user_settings, $section) : $this->_user_settings;
    }

    /**
     * Set user settings
     * @param string $section
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set_user_settings($section, $name, $value) {
        if (!is_array($this->_user_settings))
            $this->_user_settings = $this->get_user_settings();

        $this->_user_settings[$section][$name] = $value;

        return !!$this->db->where('user_id', $this->_user->user_id)->update('user_settings', [
                    'settings' => json_encode($this->_user_settings),
        ]);
    }

    /**
     * Set current menu selected
     * @param string $string - menu uri
     * @return \MY_Controller
     */
    protected function set_current_menu_path($string) {
        $this->force_current_menu_path = $string;
        return $this;
    }

    /**
     * Render json
     * @param array $data - data for json
     */
    protected function render_json(array $data) {
        echo json_encode($data);
    }

}
