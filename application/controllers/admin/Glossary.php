<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Widget_storage' . EXT;

/**
 * Glossary controller
 */
class Glossary extends MY_Controller {

    private $path = '/admin/glossary/';

    /**
     * default model
     * @var \Glossary_Model 
     */
    public $Glossary_Model;

    /**
     * model
     * @var \Handbks_Model 
     */
    public $Handbks_Model;

    /**
     * model
     * @var \File_Categories
     */
    public $File_Categories;

    /**
     * model
     * @var \Image_Albums 
     */
    public $Image_Albums;

    public function __construct() {
        parent::__construct();
        // load models
        $this->load->model('Glossary_Model');
        $this->load->model('Handbks_Model');
        $this->load->model('File_Categories');
        $this->load->model('Image_Albums');
        // load libraries
        $this->load->library('pagination');

        $this->set_breadcrumb($this->title = 'Картотека', $this->path);
    }

    /**
     * list of glossary
     */
    public function index() {
        /*
        $get = $this->input->get();

        $sort = [
            'sort_by' => array_get($get, 'sort_by', 'glossary_id'),
            'sort_direction' => array_get($get, 'sort_direction', 'desc')
        ];

        $list = $this->Glossary_Model->search(array_merge($get, array(
            'order' => $sort['sort_by'],
            'offset' => (int) array_get($get, 'per_page'),
            'limit' => $limit = $this->pagination->get_pagination_limit(),
            'order_direction' => $sort['sort_direction'],
            'with' => ['handbks' => ['`table`', 'primary_key']],
        )));
        $total_rows = $this->Glossary_Model->found_rows();
        
//        vdump($list);

        // get related handbk data
        $handbk_tree = simple_tree_group($list, 'handbk_id');
        $handbk_objects = array();
        foreach ($handbk_tree as $handbk_id => $items) {
            if (!$handbk_id)
                continue;
            
            $primary_key = array_get(array_get($items, 0), 'primary_key', 'id');
            
            foreach ($items as $it)
                $handbk_objects[$handbk_id][] = $it['object_id'];
            
//            vdump($handbk_objects);
            
            $objects = simple_tree($this->Handbks_Model->get_related_handbk(array(
                        'handbk_id' => $handbk_id,
                        'where' => array(
                            array($primary_key => $handbk_objects[$handbk_id],)
                ))), $primary_key);
            
//            vdump($var)

            $handbk = $this->Handbks_Model->get_by_primary_key($handbk_id);

            $handbk_objects[$handbk_id] = $handbk;
            $handbk_objects[$handbk_id]['objects'] = $objects;
        }
        
//        vdump($handbk_objects);
        
        foreach ($list as $key => $value) {
            if ($value['handbk_id'] && $value['object_id']) {
                if (isset($handbk_objects[$value['handbk_id']]['name']) && isset($handbk_objects[$value['handbk_id']]['objects'][$value['object_id']]['name']))
                    $list[$key]['relation'] = $handbk_objects[$value['handbk_id']]['name'] . ' / ' . $handbk_objects[$value['handbk_id']]['objects'][$value['object_id']]['name'];
                else
                    $list[$key]['relation'] = 'Неизвестная связь.';
            } else {
                $list[$key]['relation'] = '';
            }
        }

        // pagination
        $this->pagination->initialize(array(
            'base_url' => $this->path . '/?' . http_build_query_with_arrays(array_except($get, 'per_page')),
            'total_rows' => $total_rows,
            'per_page' => $limit,
            'page_query_string' => TRUE,
        ));
        
        $parents = $this->Glossary_Model->search(['only_parents' => TRUE]);
        
        foreach ($parents as $key => $it){
            $parents[$key]['value'] = $it['glossary_id'];
            $parents[$key]['title'] = $it['name'];
        }
*/
        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(
            'filters' => $this->load->view($this->template_dir . 'filters/filters', array(
                'filters' => array(
                    $this->load->view($this->template_dir . 'filters/search', array('autocomplete' => 'glossary'), TRUE),
                    $this->load->view($this->template_dir . 'filters/select', [
                        'title' => 'Справочник',
                        'field_name' => 'handbk_related',
                        'data' => [['value' => 1, 'title' => 'Да'], ['value' => 0, 'title' => 'Нет']]
                            ], TRUE),
                    $this->load->view($this->template_dir . 'filters/status', array('status' => $status_list = $this->Glossary_Model->get_status_list()), TRUE),
                    $this->load->view($this->template_dir . 'filters/select', [
                        'title' => 'Родитель',
                        'field_name' => 'parent_id',
                        'data' => $parents
                            ], TRUE),
                ),
                    ), TRUE),
            'list' => $list,
            'pagination' => $this->pagination->create_links(),
            'sort' => $sort,
            'path' => $this->path,
            'columns' => [
                'glossary_id' => ['title' => 'ID'],
                'status' => ['title' => 'Статус',],
                'name' => ['title' => 'Название', 'build_view' => function(array $it) {
                        return html_tag('a', ['href' => '/admin/glossary/edit/' . array_get($it, 'glossary_id')], array_get($it, 'name'));
                    }],
                'parent_name' => ['title' => 'Родитель'],
                'relation' => ['title' => 'Связь со справочником', 'no_sort' => TRUE],
                'created' => ['title' => 'Дата создания', 'decorate' => 'date'],
                'updated' => ['title' => 'Дата изменения', 'decorate' => 'date'],
                'delete' => ['title' => '', 'decorate' => '', 'data' => [
                        'object_type' => 'glossary',
                        'id' => function (array $it) {
                            return array_get($it, 'glossary_id');
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
     * add item
     */
    public function add() {
        $this->title = 'Добавление';

        $get = $this->input->get();
        $post = $this->input->post();

        if (!empty($post)) {

            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('description', 'Описание', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                $name = $this->input->post('name');

                $array = array(
                    'name' => $name,
                    'alias' => $alias = transliteration($name),
                    'description' => $this->input->post('description'),
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_keywords' => $this->input->post('meta_keywords'),
                    'meta_description' => $this->input->post('meta_description'),
                    'parent_id' => (int) $this->input->post('parent_id'),
                    'status' => (int) $this->input->post('status'),
                    'object_id' => (int) $this->input->post('object_id'),
                    'created' => date("Y-m-d H:i:s"),
                );

                if (!!($handbk_id = (int) $this->input->post('handbk_id')))
                    $array['handbk_id'] = $handbk_id;

                // @todo check alias
                $id = $this->Glossary_Model->insert($array);
                if ($id) {
                    if (!element('close', $get))
                        redirect('admin/glossary/edit/' . $id);
                    else
                        redirect('admin/glossary/');
                }
            }
        }

        $parents_list = $this->Glossary_Model->search(array(
            'parent_id' => 0,
            'order' => 'name',
            'status' => MY_Model::STATUS_ACTIVE,
                ), FALSE);

        $this->content = $this->load->view($this->template_dir . 'forms/glossary', array(
            'item' => $post,
            'parents_list' => $parents_list,
            'handbks' => $this->Handbks_Model->get_all(array(
                'status' => MY_Model::STATUS_ACTIVE,
                'order' => 'name',
                'where' => array(array('table !=' => $this->Glossary_Model->get_table()))
            )),
            'status' => $this->Glossary_Model->get_status_list(),
                ), TRUE);

        $this
                ->set_breadcrumb($this->title)
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ])
                ->set_scripts_bottom('/js/glossary.js');

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'breadcrumbs' => $this->get_breadcrumb(),
        ];
        
        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    /**
     * edit item
     * @param int $glossary_id
     */
    public function edit($glossary_id) {

        if (!($item = $this->Glossary_Model->get_by_primary_key((int) $glossary_id)))
            show_404();
        
        // get & check category
        $category = $this->File_Categories->get_by_field('prefix', $this->Glossary_Model->get_table(), TRUE);
        if (!($file_category_id = element('file_category_id', $category)))
            show_404();

        $get = $this->input->get();
        $post = $this->input->post();

        if (!empty($post)) {

            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('description', 'Описание', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                $name = $this->input->post('name');
                
                $array = array(
                    'name' => $name,
                    'alias' => $alias = transliteration($name),
                    'description' => $this->input->post('description'),
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_keywords' => $this->input->post('meta_keywords'),
                    'meta_description' => $this->input->post('meta_description'),
                    'parent_id' => (int) $this->input->post('parent_id'),
                    'status' => (int) $this->input->post('status'),
                    'object_id' => (int) $this->input->post('object_id'),
                    'created' => date("Y-m-d H:i:s"),
                );

                if (!!($handbk_id = (int) $this->input->post('handbk_id')))
                    $array['handbk_id'] = $handbk_id;

                // @todo check alias
                $this->Glossary_Model->update_by_primary_key($glossary_id, $array);

                if (!element('close', $get))
                    redirect('admin/glossary/edit/' . $glossary_id);
                else
                    redirect('admin/glossary/');
            }
        }

        $parents_list = $this->Glossary_Model->search(array(
            'parent_id' => 0,
            'not_id' => (int) $glossary_id,
            'order' => 'name',
            'status' => MY_Model::STATUS_ACTIVE,
                ), FALSE);
        
        $w_main = new Widget_storage(array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));
        
        $content = array(
            'item' => is_array($post) ? array_merge($item, $post) : $item,
            'parents_list' => $parents_list,
            'handbks' => $this->Handbks_Model->get_all(array(
                'order' => 'name',
                'where' => array(array('table !=' => $this->Glossary_Model->get_table())),
                'status' => MY_Model::STATUS_ACTIVE,
            )),
            'status' => $this->Glossary_Model->get_status_list(),
            'widget_storage' => $w_main->render(),
            'albums' => $this->_albums($glossary_id, $file_category_id),
        );
        

        $this
                ->set_breadcrumb($this->title = array_get($item, 'name'))
                ->set_scripts([
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ])
                ->set_scripts_bottom('/js/glossary.js');

        $data = [
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'forms/glossary', $content, TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

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
