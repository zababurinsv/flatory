<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Widget_storage' . EXT;

/**
 * Organizations controller
 */
class Organizations extends MY_Controller {

    private $path = '/admin/organizations/';

    /**
     * Organizations model
     * @var \Organizations_Model 
     */
    public $Organizations_Model;

    /**
     * Tags_Model
     * @var \Tags_Model 
     */
    public $Tags_Model;

    /**
     * Storage_Files model
     * @var \Storage_Files
     */
    public $Storage_Files;

    /**
     * File_Categories model
     * @var \File_Categories
     */
    public $File_Categories;

    /**
     * Model images albums
     * @var \Image_Albums 
     */
    public $Image_Albums;

    public function __construct() {
        parent::__construct();

        $this->load->model('Organizations_Model');
        $this->load->model('Tags_Model');
        $this->load->model('Storage_Files');
        $this->load->model('File_Categories');
        $this->load->model('Image_Albums');

        $this->load->library('pagination');

        $this->set_breadcrumb($this->title = 'Организации', $this->path);
    }

    /**
     * Organizations list
     */
    public function index() {

        $get = $this->input->get();

        $sort = [
            'sort_by' => array_get($get, 'sort_by', 'organization_id'),
            'sort_direction' => array_get($get, 'sort_direction', 'desc')
        ];

//        $organisations = $this->Organizations_Model->search(array_merge($get, array(
//            'order' => $sort['sort_by'],
//            'offset' => (int) array_get($get, 'per_page'),
//            'limit' => $limit = $this->pagination->get_pagination_limit(),
//            'order_direction' => $sort['sort_direction'],
//        )));
//        $total_rows = $this->Organizations_Model->found_rows();
        // pagination
//        $this->pagination->initialize(array(
//            'base_url' => $this->path . '/?' . http_build_query_with_arrays(array_except($get, 'per_page')),
//            'total_rows' => $total_rows,
//            'per_page' => $limit,
//            'page_query_string' => TRUE,
//        ));
//        $pagination = $this->pagination->create_links();

        $tags = $this->Tags_Model->get_tags(FALSE);
        $status_list = $this->Organizations_Model->get_status_list();

        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(
            'filters' => $this->load->view($this->template_dir . 'filters/filters', array(
                'filters' => array(
                    $this->load->view($this->template_dir . 'filters/search', array('autocomplete' => 'organizations'), TRUE),
                    $this->load->view($this->template_dir . 'filters/tags', array(
                        'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags)
                            ), TRUE),
                    $this->load->view($this->template_dir . 'filters/data_range', array(), TRUE),
                    $this->load->view($this->template_dir . 'filters/organization_types', array(
                        'organization_types' => array_merge($this->Organizations_Model->get_types(), array(array('organization_type_id' => 'all', 'name' => 'Все типы'))),
                            ), TRUE),
                    $this->load->view($this->template_dir . 'elements/status_filter', [
                        'status_list' => $status_list,
                        'data' => $get,
                            ], TRUE)
                ),
                    ), TRUE),
            'list' => $organisations,
            'pagination' => $pagination,
            'sort' => $sort,
            'path' => $this->path,
            'columns' => [
                'organization_id' => ['title' => 'ID'],
                'status' => ['title' => 'Статус',],
                'name' => ['title' => 'Название', 'build_view' => function(array $it) {
                        return html_tag('a', ['href' => '/admin/organizations/edit/' . array_get($it, 'organization_id')], array_get($it, 'name'));
                    }],
                'organization_types' => ['title' => 'Тип организации'],
                'created' => ['title' => 'Дата создания', 'decorate' => 'date'],
                'updated' => ['title' => 'Дата изменения', 'decorate' => 'date'],
                'delete' => ['title' => '', 'decorate' => '', 'data' => [
                        'object_type' => 'organization',
                        'id' => function (array $it) {
                            return array_get($it, 'organization_id');
                        },
                    ]
                ],
            ],
            'status_list' => $status_list,
            'btn_nav' => $this->load->view($this->template_dir . 'navs/btn_nav', [
                'list' => [
                    'create' => [
                        'url' => $this->path . 'add/',
                        'title' => 'Добавить',
                        'glyphicon' => 'glyphicon-plus',
                        'type' => 'info',
                    ]
                ],
                    ], TRUE)
                ), TRUE);

        $this->render();
    }

    /**
     * Add
     */
    public function add() {
        $this->title = 'Добавление';

        $get = xss_clean($this->input->get());

        // get & check category
        $category = $this->File_Categories->get_by_field('prefix', 'organizations', TRUE);
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        $errors = array();
        $post = $this->input->post();

        if ($post) {
            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                $name = $this->input->post('name');
                $file_id = (int) $this->input->post('file_id');

                $array = array(
                    'name' => $name,
                    'alias' => $alias = transliteration($name),
                    'params' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($this->input->post('params'), JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($this->input->post('params')),
                    'description' => $this->input->post('description'),
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_keywords' => $this->input->post('meta_keywords'),
                    'meta_description' => $this->input->post('meta_description'),
                    'file_id' => !!$file_id ? $file_id : NULL,
                    'created' => date("Y-m-d H:i:s"),
                    'status' => array_get($post, 'status'),
                );

                $id = $this->Organizations_Model->insert($array);
                $item = $this->Organizations_Model->get_by_primary_key($id);

                // set file involves
                if (!empty($item)) {
                    if ($file_id)
                        $this->Storage_Files->add_file_involve($file_id, $file_category_id, $id, $item['alias']);

                    // save tags
                    $tags = explode('|', element('tags', $post, ''));
                    $tags = $this->Tags_Model->update_tags($tags);
                    // set file tags
                    $this->Organizations_Model->set_tags($id, $tags);

                    // set types
                    $this->Organizations_Model->set_organization_types($id, $this->input->post('organization_type_id'));
                }

                if (!element('close', $get))
                    redirect($this->path . 'edit/' . $id);
                else
                    redirect($this->path);
            }
        }

        $tags = $this->Tags_Model->get_tags(FALSE);
        $content = array(
            'post' => $post,
            'organization_types' => $this->Organizations_Model->get_types(),
            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
        );

        $content['image_simple_upload'] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
            'image' => $this->Storage_Files->get_by_primary_key(element('file_id', $post, 0)),
            'attr' => array('image_class' => 'thumbnail'),
            'filters' => json_encode(array('is_square' => 1)),
                ), TRUE);

        $this
                ->set_breadcrumb($this->title = array_get($category, 'name') . '. Добавление')
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
        ]);

        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images']));
        $content['widget_storage'] = $this->widget_storage->render('popup', array('is_mass_edit' => FALSE, 'is_filter' => FALSE));

        $data = [
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'forms/organization', $content, TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    /**
     * Edit
     * @param int $organization_id
     */
    public function edit($organization_id) {

        if (!$organization = $this->Organizations_Model->search(array($this->Organizations_Model->get_primary_key() => (int) $organization_id), TRUE))
            show_404();

        // get & check category
        $category = $this->File_Categories->get_by_field('prefix', 'organizations', TRUE);
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

//        vdump($organization);

        $organization['organization_types'] = $this->Organizations_Model->get_organization_types($organization_id);
        $organization['organization_type_id'] = array_keys(simple_tree($organization['organization_types'], 'organization_type_id'));

        $errors = array();
        $post = $this->input->post();
        $get = xss_clean($this->input->get());

        if ($post) {
            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                $name = $this->input->post('name');
                $file_id = (int) $this->input->post('file_id');

                $params = $this->input->post('params');

                // drop empty phones
                // @todo validation?
                if (is_array(array_get($params, 'phone'))) {
                    foreach ($params['phone'] as $key => $phone)
                        if (!$phone)
                            unset($params['phone'][$key]);
                }

                $array = array(
                    'name' => $name,
                    'alias' => $alias = transliteration($name),
                    'params' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($params, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($params),
                    'description' => $this->input->post('description'),
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_keywords' => $this->input->post('meta_keywords'),
                    'meta_description' => $this->input->post('meta_description'),
                    'file_id' => !!$file_id ? $file_id : NULL,
                    'status' => array_get($post, 'status'),
                );

                $this->Organizations_Model->update_by_primary_key($organization_id, $array);
                $item = $this->Organizations_Model->get_by_primary_key($organization_id);

                // set file involves
                if (!empty($item)) {

                    if ((int) element('file_id', $organization, 0) !== $file_id) {
                        $this->Storage_Files->delete_file_involve($file_id, $file_category_id, $organization_id);
                        $this->Storage_Files->add_file_involve($file_id, $file_category_id, $organization_id, $item['alias']);
                    }

                    // save tags
                    $tags = explode('|', element('tags', $post, ''));
                    $tags = $this->Tags_Model->update_tags($tags);
                    // set file tags
                    $this->Organizations_Model->set_tags($organization_id, $tags);

                    // set types
                    $this->Organizations_Model->set_organization_types($organization_id, $this->input->post('organization_type_id'));
                }

                if (!element('close', $get))
                    redirect($this->path . 'edit/' . (int) $organization_id);
                else
                    redirect($this->path);
            }
        }

        $post = is_array($post) ? array_merge($organization, $post) : $organization;

        $tags = $this->Tags_Model->get_tags(FALSE);

        $organization_tags = $this->Organizations_Model->get_tags($organization_id);
        $post['tags'] = implode('|', $organization_tags);

        $w_popap = new Widget_storage(array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'filters']));
        $w_main = new Widget_storage(array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));

        $content = array(
            'post' => $post,
            'organization_types' => $this->Organizations_Model->get_types(),
            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
            'status' => $this->Organizations_Model->get_status_list(),
            'widget_storage' => $w_popap->render('popup', array('is_mass_edit' => FALSE, 'is_filter' => TRUE)) . $w_main->render(),
            'albums' => $this->_albums($organization_id, $file_category_id),
        );

        $content['image_simple_upload'] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
            'image' => $this->Storage_Files->get_by_primary_key(element('file_id', $post, 0)),
            'attr' => array('image_class' => 'thumbnail'),
            'filters' => json_encode(array('is_square' => 1)),
                ), TRUE);

        $this
                ->set_breadcrumb($this->title = array_get($post, 'name'))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'forms/organization', $content, TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id));
        $this->content .= $this->widget_storage->render('popup', array('is_mass_edit' => FALSE)); // 'is_filter' => FALSE, 

        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    /**
     * Prepare for albums
     * @param int $object_id - id for object
     * @param int $file_category_id - id for file category 
     */
    private function _albums($object_id = 0, $file_category_id = 0) {
        // get form errors from album create
        $old = $this->session->userdata('post_album_create_' . (int) $object_id);
        if (!$old) {
            $form = $errors = array();
        } else {
            $form = element('form', $old, array());
            $errors = element('errors', $old, array());
        }

        $form['object_id'] = $object_id;
        $form['file_category_id'] = $file_category_id;

        // get albums
        $albums = $this->Image_Albums->get_by_object_id((int) $object_id, $file_category_id);
        if (!empty($albums)) {
            $albums = simple_tree($albums, 'image_album_id');
            $image_albums = $this->Image_Albums->get_with_images_by_object_id((int) $object_id, $file_category_id);
            foreach ($image_albums as $key => $image) {
                $albums[$image['image_album_id']]['images'][] = $image;
            }
            // render album content
            foreach ($albums as $key => $album) {
                $album_images = element('images', $album, array());
                $albums[$key]['count'] = count($album_images);
                $albums[$key]['content'] = $this->load->view($this->template_dir
                        . 'widgets/tile_galery_images', array('images' => $album_images, 'list_class' => 'tile_handles'), TRUE);
            }
        }

        $this->set_html_tpls($this->load->view($this->template_dir . 'html_tpls/card__item_view__tile', array(), TRUE));
        // add widget files
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id));

        $content = $this->load->view($this->template_dir . 'objects/albums', array(
            'form' => $this->load->view($this->template_dir . 'forms/album', array('form' => $form, 'errors' => ($errors = json_encode($errors))), TRUE),
            'albums' => $albums,
            'widget' => $this->widget_storage->render(),
            'is_show_tag' => TRUE,
                ), TRUE);
        // controller js
        $this->set_scripts_bottom('/js/object_cart.js');

        $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => $errors), TRUE);
        return $content;
    }

}
