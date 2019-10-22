<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Widget_storage' . EXT;

//var_dump('ffff');die;
/**
 * Posts controller - news, articles, reviews
 *
 * @date 29.07.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Posts extends MY_Controller {

    private $path = '/admin/posts/';

    /**
     * Model default
     * @var \Posts_Model 
     */
    public $Posts_Model;

    /**
     * Model tags
     * @var \Tags_Model 
     */
    public $Tags_Model;

    /**
     * Model categories
     * @var \File_Categories 
     */
    public $File_Categories;

    /**
     * Model storage
     * @var \Storage_Files 
     */
    public $Storage_Files;

    /**
     * Model images albums
     * @var \Image_Albums 
     */
    public $Image_Albums;

    public function __construct() {
        parent::__construct();
        $this->load->model('Posts_Model');
        $this->load->model('Tags_Model');
        $this->load->model('File_Categories');
        $this->load->model('Storage_Files');
        $this->load->model('Image_Albums');

        $this->load->library('pagination');

        $this->set_breadcrumb('Записи', '/admin/posts/');
    }

    public function index() {
        $this->title = 'Записи';

        $get = $this->input->get(NULL, TRUE);

        $sort = [
            'sort_by' => array_get($get, 'sort_by', 'post_id'),
            'sort_direction' => array_get($get, 'sort_direction', 'desc')
        ];

        $posts = $this->Posts_Model->search(array_merge($get, array(
            'with' => ['file_category_name', 'file_category_alias'],
            'order' => $sort['sort_by'],
            'offset' => (int) array_get($get, 'per_page'),
            'limit' => $limit = $this->pagination->get_pagination_limit(),
            'order_direction' => $sort['sort_direction'],
        )));
        $total_rows = $this->Posts_Model->found_rows();

        // pagination
        $pagination = $this->pagination->initialize(array(
                    'base_url' => $this->path . '?' . http_build_query(array_except($get, 'per_page')),
                    'total_rows' => $total_rows,
                    'per_page' => $limit,
                    'page_query_string' => TRUE,
                ))->create_links();


        // prepare categories
        $categories = $this->File_Categories->get_by_field('prefix', $this->Posts_Model->get_types(), false);
        foreach ($categories as $key => $item) {
            $categories[$key]['title'] = array_get($item, 'name');
            $categories[$key]['url'] = '/admin/posts/add/' . array_get($item, 'prefix');
        }

        $tags = $this->Tags_Model->get_tags(FALSE);
        $status_list = $this->Posts_Model->get_status_list();

        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(
            'filters' => $this->load->view($this->template_dir . 'forms/post_filters', array(
                'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
                'categories' => $categories,
                'status' => $status_list,
                    ), TRUE),
            'columns' => [
                'post_id' => ['title' => 'ID'],
                'status' => ['title' => 'Статус',],
                'name' => ['title' => 'Название', 'view_path' => ''],
                'file_category_name' => ['title' => 'Категория'],
                'created' => ['title' => 'Дата создания', 'decorate' => 'date'],
                'updated' => ['title' => 'Дата изменения', 'decorate' => 'date'],
                'delete' => ['title' => '', 'decorate' => '', 'data' => [
                        'object_type' => 'post',
                        'id' => function (array $it) {
                            return array_get($it, 'post_id');
                        },
                    ]
                ],
            ],
            'path_table_row_template' => $this->template_dir . 'elements/posts_table_row',
            'list' => $posts,
            'content_vars' => [
                'status_list' => $status_list,
            ],
            'pagination' => $pagination,
            'sort' => $sort,
            'btn_nav' => $this->load->view($this->template_dir . 'elements/btn_dropdown', [
                'list' => $categories,
                'title' => 'Добавить',
                'glyphicon' => 'glyphicon-plus',
                'type' => 'info',
                    ], TRUE)
                ), TRUE);

        $this->set_scripts_bottom('/js/modules/post_list.js')->render();
    }

    /**
     * list of posts by type
     * @param string $type
     */
    public function type_items($type) {
        // check type
        if (!$type || !in_array($type, $this->Posts_Model->get_types()))
            show_404();

        // get & check category
        $category = $this->File_Categories->get_by_field('prefix', $type, TRUE);
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        $get = xss_clean($_GET);

        $this->title = element('name', $category, '');

        $posts = $this->Posts_Model->search(array_merge($get, array(
            'file_category_id' => $file_category_id,
            'order' => element('sort_by', $get, FALSE),
            'offset' => (int) element('per_page', $get, 0),
            'order_direction' => element('sort_direction', $get, FALSE),
        )));
        $total_rows = $this->Posts_Model->found_rows();

        // pagination
        $this->pagination->initialize(array(
            'base_url' => $this->path . $type . '/?' . http_build_query(array_except($get, 'per_page')),
            'total_rows' => $total_rows,
            'per_page' => $this->Posts_Model->limit,
            'page_query_string' => TRUE,
        ));
        $pagination = $this->pagination->create_links();
        if ($pagination) {
            $get['per_page'] = -1;
            $pagination .= anchor($this->path . $type . '/?' . http_build_query($get), 'Все ' . $total_rows, array('class' => 'pagination_btn pull-right '));
        }

        $tags = $this->Tags_Model->get_tags(FALSE);

        $this->content = $this->load->view($this->template_dir . 'pages/posts', array(
            'filters' => $this->load->view($this->template_dir . 'forms/post_filters', array(
                'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
                    ), TRUE),
            'posts' => $posts,
            'type' => $type,
            'pagination' => $pagination,
                ), TRUE);

        $this->set_scripts(array(
            'jquery-1.10.2.min.js',
            'functions.js',
            '/front/forms.js',
            'jquery-ui-1.9.2.min.js',
            'tag-it.min.js',
            'moment.min.js',
            'daterangepicker.js',
        ));

        $this->set_styles(array(
            'jquery-ui.min.css',
            'daterangepicker-bs3.css',
            'jquery.tagit.css',
        ));

        $this->set_scripts_bottom(array(
            'widget_mass_edit_image.js',
            'fl/fl_filter.js',
        ));

        $this->render();
    }

    /**
     * Add post
     * @param string $type
     */
    public function add($type) {
        // check type
        if (!$type || !in_array($type, $this->Posts_Model->get_types()))
            show_404();

        // get & check category
        $category = $this->File_Categories->get_by_field('prefix', $type, TRUE);
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        $type_object_relations = $this->Posts_Model->get_type_object_relations($type);

        $errors = [];
        $post = $this->input->post();

        $is_require_anons = in_array($type, ['news', 'articles', 'reviews']);

        if (!!$post) {

            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('alias', 'Алиас', 'required|alias');
            if ($is_require_anons)
                $this->form_validation->set_rules('anons', 'Анонс', 'required');

            if (($is_require_image = (!$type_object_relations || array_get($type_object_relations, 'is_require_image') === TRUE))) {
                $this->form_validation->set_rules('file_id', 'Фото', 'required|greater_than[0]');
                $this->form_validation->set_message('greater_than', 'Необходимо выбрать или загрузить фото.');
            }


            if (isset($post['object_id']))
                $this->form_validation->set_rules('object_id', 'Объект', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {

                // проверка существование алиаса
                if ($this->Posts_Model->search(['alias' => $post['alias'], 'file_category_id' => $file_category_id], TRUE)) {
                    $errors['alias'] = 'Такой алиас уже существует!';
                } else {

                    $name = $this->input->post('name');
                    $file_id = (int) $this->input->post('file_id');

                    $array = array(
                        'name' => $name,
                        'alias' => array_get($post, 'alias', ''),
                        'anons' => $this->input->post('anons'),
                        'content' => $this->input->post('content'),
                        'file_category_id' => $file_category_id,
                        'status' => $this->input->post('status'),
                        'file_id' => $file_id,
                        'title' => $this->input->post('title'),
                        'keywords' => $this->input->post('keywords'),
                        'description' => $this->input->post('description'),
                        'created' => date("Y-m-d H:i:s"),
                        'status' => !array_get($post, 'status') ? Posts_Model::STATUS_NOT_PUBLISHED : $post['status'],
                    );

                    if (is_numeric(array_get($post, 'object_id')))
                        $array['object_id'] = (int) $post['object_id'];

                    $id = $this->Posts_Model->insert($array);
                    $item = $this->Posts_Model->get_by_primary_key($id);

                    // set file involves
                    if (!empty($item)) {
                        $this->Storage_Files->add_file_involve($file_id, $file_category_id, $id, $item['alias']);

                        // save tags
                        $tags = explode('|', element('tags', $post, ''));
                        $tags = $this->Tags_Model->update_tags($tags);
                        // set file tags
                        $this->Posts_Model->set_post_tags($id, $tags);
                    }

                    redirect('admin/posts/edit/' . $id);
                }
            }
        }

        // object relations
        if (is_array($type_object_relations) && !!$type_object_relations) {

            // exept exitst posts by objects
            if (!!($exists_posts_by_cat = simple_tree($this->Posts_Model->get_all(['where' => [['file_category_id' => $file_category_id]]]), 'object_id')))
                $type_object_relations['where_not'] = [[array_get($type_object_relations, 'primary_key', '') => array_keys($exists_posts_by_cat)]];

            $objects = $this->Posts_Model->get_all($type_object_relations);
        } else {
            $objects = [];
        }


        $tags = $this->Tags_Model->get_tags(FALSE);
        $post_tags = element('tags', $post, '');
        $content = array(
            'objects' => $objects,
            'type_object_relations' => $type_object_relations,
            'post' => $post,
            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
            'is_require_anons' => $is_require_anons,
            'status' => $this->Posts_Model->get_status_list(),
        );

        $content['image_simple_upload'] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
            'image' => $this->Storage_Files->get_by_primary_key(element('file_id', $post, 0)),
            'attr' => array('image_class' => 'thumbnail'),
            'filters' => json_encode(array('is_square' => 1)),
                ), TRUE);

//        $this->scripts = array('jquery-1.10.2.min.js', 'functions.js', '/front/forms.js', '/ckeditor/ckeditor.js', '/ckeditor/config.js', '/ckeditor/styles.js', 'jquery-ui-1.9.2.min.js', 'tag-it.min.js',);
//        $this->set_scripts_bottom(array('doT.min.js', 'fileuploader.js', 'upload.js',));
//        $this->styles[] = 'jquery-ui.min.css';
//        $this->styles[] = 'jquery.tagit.css';

        $this
                ->set_breadcrumb($this->title = array_get($category, 'name') . '. Добавление')
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
        ]);

        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'filters']));
        $content['widget_storage'] = $this->widget_storage->render('popup', array('is_mass_edit' => FALSE, 'is_filter' => TRUE));

        $data = [
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'forms/post', $content, TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];
        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this
                ->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    /**
     * Edit post
     * @param int $post_id
     */
    public function edit($post_id) {

        if (!($_post = $this->Posts_Model->get_post($post_id)))
            show_404();

        // get & check category
        $category = $this->File_Categories->get_by_primary_key(element('file_category_id', $_post, 0));
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        $type = array_get($category, 'prefix', '');
        $type_object_relations = $this->Posts_Model->get_type_object_relations($type);

        $is_require_anons = in_array($type, ['news', 'articles', 'reviews']);

        $errors = array();

        if (!!($post = $this->input->post())) {

            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('alias', 'Алиас', 'required|alias');

            if ($is_require_anons)
                $this->form_validation->set_rules('anons', 'Анонс', 'required');

            if (($is_require_image = (!$type_object_relations || array_get($type_object_relations, 'is_require_image') === TRUE))) {
                $this->form_validation->set_rules('file_id', 'Фото', 'required|greater_than[0]');
                $this->form_validation->set_message('greater_than', 'Необходимо выбрать или загрузить фото.');
            }

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {

                // проверка существование алиаса
                if ($this->Posts_Model->search(['alias' => $post['alias'], 'file_category_id' => $file_category_id, 'not_post_id' => $post_id], TRUE)) {
                    $errors['alias'] = 'Такой алиас уже существует!';
                } else {
                    $name = $this->input->post('name');
                    $file_id = (int) $this->input->post('file_id');

                    $array = array(
                        'name' => $name,
                        'alias' => $this->input->post('alias'),
                        'anons' => $this->input->post('anons'),
                        'content' => $this->input->post('content'),
                        'status' => $this->input->post('status'),
                        'file_id' => $file_id,
                        'title' => $this->input->post('title'),
                        'keywords' => $this->input->post('keywords'),
                        'description' => $this->input->post('description'),
                    );

                    $this->Posts_Model->update_by_primary_key($post_id, $array);
                    $item = $this->Posts_Model->get_by_primary_key($post_id);

                    // set file involves
                    if (!empty($item)) {

                        if ((int) element('file_id', $_post, 0) !== $file_id) {
                            $this->Storage_Files->delete_file_involve($file_id, $file_category_id, $post_id);
                            $this->Storage_Files->add_file_involve($file_id, $file_category_id, $post_id, $item['alias']);
                        }


                        // save tags
                        $tags = explode('|', element('tags', $_POST, ''));
                        $tags = $this->Tags_Model->update_tags($tags);
                        // set file tags
                        $this->Posts_Model->set_post_tags($post_id, $tags);
                    }

                    redirect('admin/posts/edit/' . (int) $post_id);
                }
            }
        }

//        $this->scripts = array('jquery-1.10.2.min.js', 'functions.js', '/front/forms.js', '/ckeditor/ckeditor.js', '/ckeditor/config.js', '/ckeditor/styles.js', 'jquery-ui-1.9.2.min.js', 'tag-it.min.js',);
//        $this->set_scripts_bottom(array('doT.min.js', 'fileuploader.js', 'upload.js',));
//        $this->styles[] = 'jquery-ui.min.css';
//        $this->styles[] = 'jquery.tagit.css';

        $tags = $this->Tags_Model->get_tags(FALSE);
        $post_tags = $this->Posts_Model->get_post_tags($post_id);

        $post['tags'] = implode('|', $post_tags);

        $w_popap = new Widget_storage(array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'filters']));
        $w_main = new Widget_storage(array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));

        $content = array(
            'category' => $category,
            'post' => is_array($post) && !!$post ? array_merge($_post, $post) : $_post,
            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
            // не все объекты имеют предварительный просмотр! и на фронте не все можно глянуть
            'is_access_preview' => $is_access_preview = (!$type_object_relations || array_get($type_object_relations, 'is_access_preview') === TRUE),
            'is_require_anons' => $is_require_anons,
            'status' => $this->Posts_Model->get_status_list(),
            'widget_storage' => $w_popap->render('popup', array('is_mass_edit' => FALSE, 'is_filter' => TRUE)) . $w_main->render(),
            'albums' => $this->_albums($post_id, $file_category_id),
        );

        if (!!($object_id = (int) array_get($_post, 'object_id'))) {
            // object relations
            if (!!$type_object_relations && is_array($type_object_relations)) {

                $type_object_relations = array_merge($type_object_relations, [
                    'where' => [[array_get($type_object_relations, 'primary_key', 'primary_key') => $object_id],],
                    'is_row' => TRUE,
                ]);

                $content['object_name'] = array_get($this->Posts_Model->get_all($type_object_relations), array_get($type_object_relations, 'label', ''), '');
            }
        }


        $content['image_simple_upload'] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
            'image' => $this->Storage_Files->get_by_primary_key(element('file_id', $_post, 0)),
            'attr' => array('image_class' => 'thumbnail'),
            'filters' => json_encode(array('is_square' => 1)), // {"is_square":"1"}
                ), TRUE);

        $this
                ->set_breadcrumb($this->title = array_get($_post, 'category_name') . '. Добавление')
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'forms/post', $content, TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
            'is_access_preview' => $is_access_preview,
            'category' => $category,
            'post' => $_post,
        ];
        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this
                ->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    /**
     * Delete post
     * @param int $post_id
     */
    public function delete($post_id) {
        if (!($post = $this->Posts_Model->get_post($post_id)))
            show_404();

        // get & check category
        $category = $this->File_Categories->get_by_primary_key(element('file_category_id', $post, 0));
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        // delete item
        $this->Posts_Model->update_by_primary_key((int) $post_id, array('status' => MY_Model::STATUS_DELETED));
        redirect('admin/posts/' . element('prefix', $category, ''));
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

        $content = $this->load->view($this->template_dir . 'objects/albums', array(
            'form' => $this->load->view($this->template_dir . 'forms/album', array('form' => $form, 'errors' => ($errors = json_encode($errors))), TRUE),
            'albums' => $albums,
            'is_show_tag' => TRUE,
                ), TRUE);
        // controller js
        $this->set_scripts_bottom('/js/object_cart.js');

        $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => $errors), TRUE);
        return $content;
    }

}
