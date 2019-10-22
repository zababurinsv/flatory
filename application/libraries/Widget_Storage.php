<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Widget' . EXT;

/**
 * Widget_Storage
 *
 * @date 10.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Widget_storage extends Widget {

    /**
     *
     * @var int - file_category_id 
     */
    private $category;

    /**
     *
     * @var bool 
     */
    private $is_mass_edit = true;

    /**
     *
     * @var bool 
     */
    private $is_filter = true;

    /**
     *
     * @var array 
     */
    private $sections = [
        'images' => ['title' => 'Изображения', 'content' => ['filters', 'mass_editor', 'images', 'modify_nav']],
        'docs' => ['title' => 'Документы', 'content' => ['filters', 'docs', 'modify_nav']],
        'upload' => ['title' => 'Загрузить', 'content' => ['upload']],
    ];

    /**
     *
     * @var array 
     */
    private $_settings = [
        'section_default' => 'images',
    ];

    /**
     * разрешения для загрузчика
     * @var array
     */
    private $upload_access = [];

    public function __construct($params = array()) {
        parent::__construct($params);

        if (!$params || !$this->controller)
            return;

        // set widget path
        $this->_view_path = $this->controller->template_dir . $this->_view_path . 'storage' . DIRECTORY_SEPARATOR;

        $this->_settings = is_array($params) ? array_merge($this->_settings, $params) : $this->_settings;
        $this->category = (int) array_get($this->_settings, 'category');

        $this->load->model('File_Types');
        $this->load->model('Proportions');
        $this->load->model('Storage_Files');
        $this->load->model('File_Categories');

        $this->load->library('image_lib');
        $this->load->library('flpagination');


        $this->controller->set_scripts(array(
            '/js/tag-it.min.js',
            '/js/moment.min.js',
            '/js/daterangepicker.js',
            '/js/doT.min.js',
        ));
        $this->controller->set_scripts_bottom([
            '/js/widget_mass_edit_image.js',
            '/js/fl/fl_pagination.js',
            '/js/fl/fl_filter.js',
            '/js/fileuploader.js',
            '/js/upload.js',
        ]);

        // for filters
        $this->controller->set_styles(array('/css/daterangepicker-bs3.css', '/css/jquery.tagit.css'));

//        vdump(is_array($params));
//        vdump($params);
//        vdump($params['sections'], 1);
    }

    /**
     * widget update
     * @param MY_Controller $this->controller
     * @return type
     */
    public function upload() {

        $content = $this->_CI->load->view($this->_view_path . 'upload_file', [], TRUE);
        return $content;
    }

    /**
     * widget storage
     * @param MY_Controller $this->controller
     * @return type
     */
    public function storage($type = 'images') {
        return $this->controller->load->view($this->_view_path . 'storage', [], TRUE);
    }

    /**
     * Mass edit image
     * @return string \View
     */
    public function mass_edit($type = 'images') {


        if (!$this->is_mass_edit)
            return '';

        $tags = $this->controller->Tags_Model->get_tags(FALSE);

        if ($type === 'docs')
            return $this->controller->load->view($this->controller->template_dir . 'widgets/mass_edit_docs', array(
                        'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
                            ), TRUE);

        return $this->controller->load->view($this->controller->template_dir . 'widgets/mass_edit_image', array(
                    'proportions' => !$this->category ? $this->controller->db->where('status', '1')->get('proportions')->result_array() : $this->controller->Proportions->get_category_proportions($this->category),
                    'require_proportions' => !$this->category ? FALSE : TRUE,
                    'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
                        ), TRUE);
    }

    /**
     * 
     * @return string
     */
    public function filter($type = 'images') {

        if (!$this->is_filter)
            return '';

        if ($type === 'docs')
            return $this->load->view($this->_view_path . 'filters_docs', array(
                        'categories' => $this->controller->File_Categories->get_list(),
                        'file_formats' => $this->controller->db->where('file_type_id', '2')->get('file_formats')->result_array(),
                            ), TRUE);

        return $this->load->view($this->_view_path . 'filters', array(
                    'categories' => $this->controller->File_Categories->get_list(),
                    'proportions' => $this->controller->Proportions->get_list(),
                        ), TRUE);
    }

    /**
     * получаем список секций
     * @return array
     */
    private function _get_sections() {
        if (is_array($sections = array_get($this->_settings, 'sections')) && $sections) {

            $result = [];

            foreach ($sections as $it) {
                if (isset($this->sections[$it]))
                    $result[$it] = $this->sections[$it];
            }

            // переопределяем активную секцию по умолчанию
            if (!in_array($this->_settings['section_default'], $sections))
                $this->_settings['section_default'] = array_get($sections, 0, $this->_settings['section_default']);

            return $result;
        }

        return $this->sections;
    }

    /**
     * Получить контент для текущей секции
     * @param string $section - alias секции 
     * @return array
     */
    private function _get_section_content($section) {

        $content = [];

//        vdump($section, 1);
        
        switch ($section) {
            case 'upload':
                $content['upload'] = $this->upload();
                break;
            case 'images':
                $content = [
                    'images' => $this->storage(),
                    'filters' => $this->filter(),
                    'mass_editor' => $this->mass_edit(),
                ];
                array_push_unique($this->upload_access, 'images');
                break;
            case 'docs':
                $content = [
                    'docs' => $this->storage('docs'),
                    'filters_docs' => $this->filter('docs'),
                    'mass_editor_docs' => $this->mass_edit('docs'),
                ];
                array_push_unique($this->upload_access, 'docs');
                break;
        }

        return $content;
    }

    /**
     * Render widget
     * @param string $type - type of widget (index or popup)
     * @return string \View
     */
    public function render($type = '', $config = array()) {
        $type = !$type ? 'index' : ($type !== 'popup' ? 'index' : $type);

        $this->is_filter = (bool) element_strict('is_filter', $config, $this->is_filter);
        $this->is_mass_edit = (bool) element_strict('is_mass_edit', $config, $this->is_mass_edit);

        $data = [
            'sections' => $sections = $this->_get_sections(),
            'settings' => $this->_settings,
        ];
        
//        vdump($type, 1);;
//        vdump($sections, 1);

        foreach ($sections as $alias => $it) {
            $data = array_merge($data, $this->_get_section_content($alias));
            
//            vdump($this->_get_section_content($alias), 1);
        }

//        vdump($sections, 1);
//        array(
//            'upload' => $this->upload(),
//            'images' => $this->storage(),
//            'filters' => $this->filter(),
//            'filters_docs' => $this->load->view($this->_view_path . 'filters_docs', array(
//                'categories' => $this->controller->File_Categories->get_list(),
//                'file_formats' => $this->controller->db->where('file_type_id', '2')->get('file_formats')->result_array(),
//                    ), TRUE),
//            'mass_editor' => $this->mass_edit(),
//            'docs' => $this->storage('docs'),
//        );

        $content = $this->load->view($this->_view_path . $type, $data, TRUE);
        $this->controller->set_scripts_bottom('/js/widget_storage.js');
//        $this->controller->after_body .= html_tag('script', ['type' => 'text/javascript'], "$(document).on('ready', function(){ FlUpload.setAccessByType(" . json_encode($this->upload_access) . "); });");
        $this->controller->after_body .= $this->load->view($this->_view_path . 'upload_access', ['upload_access' => $this->upload_access], TRUE);
        return $content;
    }

    public function __images() {

        return $this->load->view($this->_view_path . 'images', [], TRUE);
    }

}
