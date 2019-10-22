<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Front controller
 * @date 14.10.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_FrontController extends CI_Controller {

    // Путь к файлам шаблона (как для странниц так и для assets)
    public $template_dir = 'front/';
    // Каркас шаблона (ведущий файл)
    public $template = 'layout';
    // Элементы шаблона
    public $title = 'otclick-adv';
    public $scripts = array('jquery-1.8.3.js', 'jquery-ui.min.js', 'forms.js', 'bootstrap_modules.js', 'jquery_ext.js');
    public $scripts_bottom = array('geo_index.js', 'main.js');
    public $styles = array('jquery-ui.min.css', 'style.css');
    public $styles_bottom = array();
    public $nav = false;
    public $header = false;
    public $content = false;
    public $body = '';
    public $sidebar_left = '';
    public $sidebar_right = '';
    public $footer = false;
    public $meta_keywords = '';
    public $meta_description = '';
    public $meta_copywrite = '';
    public $favicon = '/favicon.ico';
    public $after_body = false;
    // path
    private $styles_path = 'css/';
    private $scripts_path = 'js/';

    /**
     * adv banners
     * @var array 
     */
    private $banners = array();

    /**
     * breadcrumbs
     * @var array 
     */
    private $breadcrumbs = array();

    public function __construct() {
        parent::__construct();
//        $this->migration->current();
        // Делаем миграцию до последней версии
        if (!$this->migration->current()) {
            // Если произошла ошибка - выводим сообщение
            show_error($this->migration->error_string());
            die();
        }

//        var_dump('ffff');die;

//        $this->migration->version(12);
        // load helpers
        $this->load->helper('html');
        // load models
        $this->load->model('Geo');
        $this->load->model('Search_Model');
        $this->load->model('Banners_Model');
    }

    /**
     * Render view
     * @return string
     */
    public function render() {


        // load adv only on production
        $this->banners = ENVIRONMENT === 'production' ? simple_tree($this->Banners_Model->get_all(), 'position') : array();
        //Шаблон
        $this->template = $this->template_dir . $this->template;
        // path
        $this->styles_path = $this->styles_path . $this->template_dir;
        $this->scripts_path = $this->scripts_path . $this->template_dir;

        if ($this->nav === FALSE) {
            $this->nav = $this->load->view($this->template_dir . 'nav', array(), TRUE);
        }


        // если не определены необходимые компоненты (шапка, контент, подвал) - ставим дефолтные
        if ($this->header === false) {
            // создаем виджет поиска

            $search_widget = $this->load->view($this->template_dir . 'widgets/search', $this->get_search_data(), TRUE);
            $this->header = $this->load->view($this->template_dir . 'header', array(
                'nav' => $this->nav,
                'search' => $search_widget,
                'banners' => $this->banners,
//                'nav_catalog' => in_array($segment = $this->uri->segment(1), [false, 'moskva', 'novaya-moskva', 'moskovskaya-oblast', 'catalog']) ? $this->load->view($this->template_dir . 'widgets/nav_catalog', ['segment' => $segment], TRUE) : '',
                'nav_catalog' => $this->load->view($this->template_dir . 'widgets/nav_catalog', [], TRUE),
                    ), TRUE);
        }



        if ($this->content === false) {
            $this->_buid_sidebar_left();

            $this->content = $this->load->view($this->template_dir . 'content_right', array(
                'body' => $this->body,
                'sidebar_left' => $this->sidebar_left,
                'sidebar_right' => $this->sidebar_right,
                'banners' => $this->banners,
                'breadcrumbs' => $this->load->view($this->template_dir . 'widgets/breadcrumbs', array('breadcrumbs' => $this->breadcrumbs), TRUE),
                    ), TRUE);
        }
//        var_dump('ddddd');die;
        if ($this->footer === false)
            $this->footer = $this->load->view($this->template_dir . 'footer', array('nav' => $this->nav), TRUE);

        if ($this->after_body === FALSE)
            $this->after_body = '';

        // add adv widget
//        $this->after_body .= $this->load->view('adv/vbutovo/widget_call', [], TRUE);


        foreach ($this->scripts as $key => $val) {
            $this->scripts[$key] = js_file_name($val);
        }
        foreach ($this->scripts_bottom as $key => $val) {
            $this->scripts_bottom[$key] = js_file_name($val);
        }


        // подготавливаем переменные для шаблона
        $data = array(
            'title' => $this->title,
            'scripts_bottom' => $this->scripts_bottom,
            'scripts' => $this->scripts,
            'scripts_path' => $this->scripts_path,
            'styles' => $this->styles,
            'styles_bottom' => $this->styles_bottom,
            'styles_path' => $this->styles_path,
            'favicon' => $this->favicon,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'meta_copywrite' => $this->meta_copywrite,
            'header' => $this->header,
            'content' => $this->content,
            'footer' => $this->footer,
            'after_body' => $this->after_body,
        );
        return $this->load->view($this->template, $data);
    }

    /**
     * Get data for Search widget
     * @return array
     */
    public function get_search_data() {
        // old 
        $search = array();

//        var_dump('My_FrontController');
//        var_dump($this->Geo->zone);die;
//        vdump($this->input->get(), 1);
//        // search xone group list
//        $search['zone'] = $this->Geo->zone;
//        // decorate zone
//        foreach ($search['zone'] as $key => $item) {
//            if ($item->code === 'MOS')
//                $search['zone'][$key]->name = 'Область';
//        }
//        krsort($search['zone']);
//        $search['geo_direction'] = $this->Geo->get_directions();
//        $search['district'] = $this->Geo->get_locality('MOW');
//
//        // max filters values
//        $max_filters = $this->Search_Model->get_max_filters($this->Geo->get_zone_ids());
//        $search['max_filters'] = json_encode($max_filters);
//        // mkad distans filter list
//        $search['distance_to_mkad'] = $this->db->get('distance_to_mkad')->result();
        // old \

//        $search = $this->Geo->get_alphabet('square');

        return $search;
    }

    /**
     * Set scripts
     * @todo __set
     * @param array/string $scrpts
     * @return \MY_FrontController
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
     * @todo __set
     * @param array/string $scrpts
     * @return \MY_FrontController
     */
    public function set_scripts_bottom($scripts) {
        if (is_array($scripts)) {
            foreach ($scripts as $script)
                if (!in_array($script, $this->scripts_bottom))
                    $this->scripts_bottom[] = $script;
        }else {
            if (!in_array($scripts, $this->scripts_bottom))
                $this->scripts_bottom[] = $scripts;
        }
        return $this;
    }

    /**
     * Set styles
     * @todo __set
     * @param array/string $scrpts
     * @return \MY_FrontController
     */
    public function set_styles($scripts) {
        if (is_array($scripts)) {
            foreach ($scripts as $script)
                if (!in_array($script, $this->styles))
                    array_unshift($this->styles, $script);
        }else {
            if (!in_array($scripts, $this->styles))
                array_unshift($this->styles, $scripts);
        }
        return $this;
    }

    /**
     * add breadcrumbs
     * @param string $name
     * @param string $url
     * @return \MY_FrontController
     */
    public function breadcrumbs_add($name, $url = FALSE) {
        $this->breadcrumbs[] = array('name' => (string) $name, 'url' => $url);
        return $this;
    }

    private function _buid_sidebar_left() {
        $category = [];
        // create fast search
        {
            $this->sidebar_left .= $this->load->view($this->template_dir . 'widgets/fast_search', [], TRUE);
        }

        // add adv
        {
            if (is_array($banner_left_top = array_get($this->banners, 'left_top')) && $banner_left_top)
                $this->sidebar_left .= html_tag('div', array('class' => 'banner_left_top space_bottom_xxl'), array_get($banner_left_top, 'content'));
        }
        // create news
        {
            $this->load->model('Posts_Model');
            $this->load->model('File_Categories');
//            var_dump('fffff');die;
//            $category = $this->File_Categories->get_by_field('prefix', 'news');

            if (element('file_category_id', $category)) {
                $news = $this->Posts_Model->search(array(
                    'file_category_id' => $category['file_category_id'],
                    'limit' => 7,
                    'offset' => 0,
                    'status' => MY_Model::STATUS_ACTIVE,
                ));

                $this->sidebar_left .= $this->load->view($this->template_dir . 'widgets/news', array('news' => $news), TRUE);
            }
        }
    }

}
