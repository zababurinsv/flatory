<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Storage controller
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Storage extends MY_Controller {

    private $path = '/admin/storage/';

    /**
     * Model
     * @var \Storage_Files 
     */
    public $Storage_Files;
    
    /**
     *
     * @var \Widget_storage 
     */
    public $widget_storage;

    public function __construct() {
        parent::__construct();

        $this->load->model('File_Types');
        $this->load->model('File_Categories');
        $this->load->model('Storage_Files');
        $this->load->model('Proportions');
        $this->load->model('Tags_Model');

        $this->load->library('image_lib');
        $this->load->library('pagination');

        $this->set_breadcrumb($this->title = 'Файлы', '/admin/storage/');
    }

    /**
     * Default type of files
     */
    public function index() {

        $type = $this->uri->segment(3, 'images');
        
        if (!in_array($type, array('images', 'docs')))
            show_404();

        $get = xss_clean($_GET);
        $order = element('sort_by', $get, FALSE);
        $offset = (int) element('per_page', $get, 0);
        $order_direction = element('sort_direction', $get, FALSE);

        // set default filters
        $get['with_categories'] = TRUE;
        $get['with_tags'] = TRUE;
        $get['with_total_size'] = TRUE;
        $get['with_watermarks'] = TRUE;

        $files = $this->Storage_Files->get_by_file_type($type, 'alias', $get, $offset, $order, $order_direction, $limit = $this->pagination->get_pagination_limit());
        $total_rows = $this->Storage_Files->found_rows();
//        var_dump($total_rows);die;

        if($total_rows == 0){

        }else{
            $get = array_except($get, array('with_categories', 'with_tags', 'with_total_size'));

            foreach ($files as $key => $file) {
                if (element('categories', $file, FALSE))
                    $files[$key]['categories'] = explode(',', $file['categories']);

                if (element('tags', $file, FALSE))
                    $files[$key]['tags'] = explode(',', $file['tags']);
            }

            $tags = $this->Tags_Model->get_tags(FALSE);
            $this->load->library('Widget_storage', array('this' => $this));
            // pagination
            $pagination = $this->pagination->initialize(array(
                'base_url' => $this->path . $type . '/?' . http_build_query(array_except($get, 'per_page')),
                'total_rows' => $total_rows,
                'per_page' => $limit,
                'page_query_string' => TRUE,
            ))->create_links();

            $this->content = $this->load->view($this->template_dir . 'storage', array(
                'nav' => $this->_nav_file_types($type),
                'filters' => $this->widget_storage->filter($type),
                'mass_edit' => $this->widget_storage->mass_edit($type),
                'body' => $this->load->view($this->template_dir . 'pages/storage_' . $type, array(
                    'list' => $files,
                    'path' => $this->path,
                    'pagination' => $pagination,
                ), TRUE),
            ), TRUE);
        }




        // включаем фильтры
        $this->after_body .= html_tag('script', [], "$('.fl-filter').parents('.hpanel').show()");
        
        $this->set_scripts([
                    '/js/functions.js',
                    '/js/front/forms.js',
                ])
                ->set_styles(array(
                    '/css/fancybox/source/jquery.fancybox.css',
                ))->set_scripts_bottom(array(
                    '/js/widget_mass_edit_image.js',
                    '/js/fl/fl_filter.js',
                    '/js/jquery.jcarousel.min.js',
                    '/js/fancybox/source/jquery.fancybox.pack_fix_1.10.js',
                    '/js/fl/fl_gallery.js',
                ))
                ->set_current_menu_path('/admin/storage/')
                ->render();
    }

    /**
     * Images
     */
    public function images() {
        $this->index();
    }

    /**
     * Docs
     */
    public function docs() {
        $this->index();
    }

    /**
     * Card of image
     * @todo check type of file
     * @param type $name
     */
    public function card($name) {

        if (!($file = $this->Storage_Files->get_with_type($name, 'name')))
            show_404();

//        vdump($file);

        $this->set_breadcrumb($this->title = 'Изменить файл');

        $file_proportions = $this->Storage_Files->get_file_proportions($file);
        $file_proportions = !empty($file_proportions) ? simple_tree($file_proportions, 'name') : $file_proportions;
        $proportions = $this->Storage_Files->get_proportions();
        $proportions = !empty($proportions) ? simple_tree($proportions, 'name') : $proportions;

        $proportions = array_except($proportions, array_keys($file_proportions));

        $tags = $this->Tags_Model->get_tags(FALSE);
        $image_tags = $this->Storage_Files->get_file_tags($file);

        $content = $this->load->view($this->template_dir . 'pages/' . ((int) $file['file_type_id'] === Storage_Files::FILE_IMAGE ? 'storage_card_image' : 'storage_card'), array(
            'title' => $this->title,
            'breadcrumbs' => $this->get_breadcrumb(),
            'file' => $file,
            'proportions' => $proportions,
            'file_proportions' => $file_proportions,
            'file_involves' => $this->Storage_Files->get_file_involves($file),
            'total_size' => $this->Storage_Files->get_total_size($file),
            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
            'image_tags' => implode('|', $image_tags),
                ), TRUE);

        $this
                ->set_scripts([
                    '/vendor/ckeditor_small/ckeditor.js',
                    '/vendor/ckeditor_small/drop_cache.js',
                    '/vendor/ckeditor_small/config.js',
                    '/vendor/ckeditor_small/styles.js',
                ])
                ->set_scripts_bottom('/js/storage_card.js');

        $this
                ->render($content);
    }

    /**
     * Ajax save card
     */
    public function save_card() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);

        $post = xss_clean($_POST);
//        vdump($post);
        // @todo validation $post

        $file_id = (int) element('file_id', $post, 0);

        $file_update = array(
            'original_name' => element('original_name', $post, ''),
            'alt' => element('alt', $post, ''),
            'description' => element('description', $post, ''),
        );

        $save = $this->Storage_Files->update_by_primary_key($file_id, $file_update);

        if ($save && element('tags', $post, FALSE)) {
            // save tags
            $tags = explode('|', element('tags', $post, ''));
            $tags = $this->Tags_Model->update_tags($tags);
            // set file tags
            $this->Storage_Files->set_file_tags($file_id, $tags);
        }

        echo json_encode(array('success' => $save, 'data' => array(), 'errors' => array()));
        return;
    }

    /**
     * Ajax add resize for image
     */
    public function add_resize() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);

        $post = xss_clean($_POST);

        $proportion = $this->Proportions->get_by_primary_key((int) element('proportion_id', $post, 0));
        $file = $this->Storage_Files->get_with_type((int) element('file_id', $post, 0));

        if (empty($proportion) || empty($file)) {
            echo json_encode(array('success' => FALSE, 'error' => 'File or proportion not found!'));
            return FALSE;
        }

        if (!$this->Storage_Files->is_image($file)) {
            echo json_encode(array('success' => FALSE, 'error' => 'File is not image!'));
            return FALSE;
        }
        $width = element('x', $proportion, 0);
        $heigth = element('y', $proportion, 0);

        $image_lib_conf = $this->config->load('image_lib');

        // resize
        $is_resize = $this->image_lib->resize($file['file_name'], $width, $heigth);

        if ($is_resize) {
            // update relation file_proportions
            $file_path = DOCROOT . 'images' . DIRECTORY_SEPARATOR
                    . $width . element('dest_folder_size_separator', $image_lib_conf, 'x') . $heigth
                    . DIRECTORY_SEPARATOR . $file['file_name'];
            $size = filesize($file_path);
            $is_watermark = (int) element('is_watermark', $post, 0);

            $this->Storage_Files->add_file_proportion($file, $proportion, $size, $is_watermark);

            // add watermark
            if ($is_watermark)
                $is_watermark = $this->image_lib->watermark($file_path);
        }

        echo json_encode(array('success' => $is_resize, 'data' => $this->Storage_Files->get_file_proportions($file), 'error' => ''));
        return FALSE;
    }

    /**
     * Ajax del resize for image
     */
    public function del_resize() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);

        $post = xss_clean($_POST);
        // get data
        $proportion = $this->Proportions->get_by_primary_key((int) element('proportion_id', $post, 0));
        $file = $this->Storage_Files->get_with_type((int) element('file_id', $post, 0));

        if (empty($proportion) || empty($file)) {
            echo json_encode(array('success' => false, 'data' => array(), 'error' => 'Файл или пропорции изображения не найдены.'));
            return FALSE;
        }

        // проверка file involves
        $file_involves = $this->Storage_Files->get_file_involves($file);
        if (!empty($file_involves)) {
            echo json_encode(array('success' => false, 'data' => $file_involves, 'error' => 'Файл участвует в каком-то из разделов.'));
            return FALSE;
        }

        $file_proportions = $this->Storage_Files->get_file_proportions($file);
        $file_proportions = !empty($file_proportions) ? simple_tree($file_proportions, 'proportion_id') : $file_proportions;

        if (!element(element('proportion_id', $proportion, FALSE), $file_proportions, FALSE)) {
            echo json_encode(array('success' => false, 'data' => array(), 'error' => 'У фала нет данной пропорции.'));
            return FALSE;
        }

        $width = element('x', $proportion, 0);
        $heigth = element('y', $proportion, 0);

        $image_lib_conf = $this->config->load('image_lib');
        $file_path = DOCROOT . 'images' . DIRECTORY_SEPARATOR
                . $width . element('dest_folder_size_separator', $image_lib_conf, 'x') . $heigth
                . DIRECTORY_SEPARATOR . $file['file_name'];

        unlink($file_path);
        $this->Storage_Files->delete_file_proportion($file, $proportion);
        echo json_encode(array('success' => TRUE, 'data' => array(), 'error' => ''));
        return FALSE;
    }

    /**
     * Ajax widget mass edit
     */
    public function mass_edit() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);

        $post = xss_clean($_POST);

        $files = element('files', $post, array());

        if (empty($files)) {
            echo json_encode(array('success' => FALSE, 'data' => $post, 'errors' => array('files' => 'Не выбраны файлы для изменения.')));
            return;
        }

        // update file attr
        $result = $this->Storage_Files->update_files($files, $post);
        $errors = element('errors', $result, array());

        echo json_encode(array('success' => empty($errors), 'data' => $result, 'errors' => $errors));
        return;
    }

    /**
     * Create navigation
     * @return type
     */
    private function _nav_file_types($current) {
        $types = $this->File_Types->get_list();

        $list = [];

        foreach ($types as $it) {
            $list[] = [
                'url' => $this->path . $it['alias'],
                'title' => $it['name'],
                'active' => $it['alias'] === $current,
            ];
        }

        $list[] = [
            'url' => '/admin/upload',
            'title' => 'Загрузить',
        ];

        return $this->load->view($this->template_dir . 'navs/pills', array('list' => $list), TRUE);
    }

}
