<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * registry сщтекщддук
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Registry extends MY_Controller {

    /**
     *
     * @var \Registry_Model 
     */
    public $Registry_Model;

    /**
     *
     * @var \Handbks_Model 
     */
    public $Handbks_Model;

    public function __construct() {
        parent::__construct();

        $this->load->model('Registry_Model');
        $this->load->model('Handbks_Model');

        $this->set_breadcrumb($this->title = 'Реестр', $this->path = '/admin/registry/');
    }

    public function index() {


        $get = $this->input->get(NULL, TRUE);

        $list = $this->Registry_Model->search(array_merge($get, array(
            'with' => ['handbk_name', 'objects_relations', 'parse_params'],
            'order' => array_get($get, 'sort_by', FALSE),
            'offset' => (int) array_get($get, 'per_page'),
            'limit' => $limit = $this->pagination->get_pagination_limit(),
            'order_direction' => array_get($get, 'sort_direction', FALSE),
        )));
        $total_rows = $this->Registry_Model->found_rows();

        // список категорий инфраструктуры
        $infrastructure_cats = simple_tree($this->Registry_Model->search(['handbk_id' => 11]), 'registry_id');

        foreach ($list as $key => $val) {
            if ($val['params'] && ($category_id = array_get($val['params'], 'category_id')) && ($cat_name = array_get($infrastructure_cats, $category_id)))
                $list[$key]['category'] = array_get($cat_name, 'name');
            else
                $list[$key]['category'] = '';
        }

        // pagination
        $pagination = $this->pagination->initialize(array(
                    'base_url' => $this->path . '?' . http_build_query(array_except($get, 'per_page')),
                    'total_rows' => $total_rows,
                    'per_page' => $limit,
                    'page_query_string' => TRUE,
                ))->create_links();

        // prepare categories
        $categories = $this->Handbks_Model->search([
            'table' => 'registry',
            'order' => 'name',
        ]);

        foreach ($categories as $key => $item) {
            $categories[$key]['title'] = array_get($item, 'name');
            $categories[$key]['url'] = '/admin/registry/add/' . array_get($item, 'handbk_id');
        }
        
        $status_list = $this->Registry_Model->get_status_list();

        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(
            'filters' => $this->load->view($this->template_dir . 'forms/registry_filters', array(
                'categories' => $categories,
                'status' => $status_list,
                    ), TRUE),
            'columns' => [
                'registry_id' => ['title' => 'ID'],
                'status' => ['title' => 'Статус'],
                'name' => ['title' => 'Название',],
                'handbk_name' => ['title' => 'Справочник',],
                'category' => ['title' => 'Категория инфраструктуры', 'no_sort' => TRUE],
                'created' => ['title' => 'Дата создания', 'decorate' => 'date'],
//                'objects_relations' => ['attr' => ['title' => 'Связь с объектами'], 'title' => html_tag('span', ['class' => 'glyphicon glyphicon-link'])],
                'delete' => ['title' => ''],
            ],
            'path_table_row_template' => $this->template_dir . 'elements/registry_table_row',
            'content_vars' => [
                'status_list' => $status_list,
            ],
            'list' => $list,
            'pagination' => $pagination,
            'btn_nav' => $this->load->view($this->template_dir . 'elements/btn_dropdown', [
                'list' => $categories,
                'title' => 'Добавить',
                'type' => 'primary',
                    ], TRUE)
                ), TRUE);

        $this->set_scripts_bottom(array(
            '/js/fl/fl_filter.js', '/js/admin/registry.js'
        ));

        $this->render();
    }

    public function add($handbk_id) {

        if (!($handbk_id = (int) $handbk_id) || !($handbk = $this->Handbks_Model->search(['handbk_id' => $handbk_id, 'with' => ['parse_params', 'get_params_data']], TRUE)))
            show_404();

        $errors = [];
        $post = $this->input->post();

        if (!!$post) {

            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save
                $id = $this->Registry_Model->insert([
                    'name' => $post['name'],
                    'handbk_id' => $handbk_id,
                    'status' => (int)array_get($post, 'status', \Registry_Model::STATUS_ACTIVE),
                ]);

                if ($id) {

                    // создаем запись в связанной таблице (ext_table)
                    // добавляются поля из ext_table_fields и registry_id
                    if (array_get($handbk, 'params.ext_table') && !!($ext_fields = array_get($handbk, 'params.ext_table_fields')) && is_array($ext_fields)) {

                        $ext_data = [];

                        foreach ($ext_fields as $_field) {
                            $ext_data[$_field] = array_get($post, 'params.' . $_field);
                        }

                        if ($ext_data) {
                            $ext_data[$this->Registry_Model->get_primary_key()] = $id;
                            $this->Registry_Model->insert($ext_data, $handbk['params']['ext_table']);
                        }
                    }

                    redirect('/admin/registry/');
                } else {
                    $errors[] = 'Не удалось создать запись.';
                }
            }
        }

        $this->set_breadcrumb(array_get($handbk, 'name') . '. Добавление');

        $data = [
            'title' => $this->title = 'Добавление записи в справочник "' . array_get($handbk, 'name') . '"',
            'content' => $this->load->view($this->template_dir . 'forms/registry', [
                'handbk' => $handbk,
                'status_list' => $this->Registry_Model->get_status_list(),
                    ], TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this
                ->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    public function edit($registry_id) {

        if (!($registry_id = (int) $registry_id) || !($registry = $this->Registry_Model->search(['registry_id' => $registry_id, 'with' => ['parse_params']], TRUE)))
            show_404();

        $errors = [];
        $post = $this->input->post();

        $handbk = $this->Handbks_Model->search(['handbk_id' => $registry['handbk_id'], 'with' => ['parse_params', 'get_params_data']], TRUE);

        // get ext_table data
        if (($ext_table = array_get($handbk, 'params.ext_table'))) {
            $ext_data = array_except($this->Registry_Model->get_by_id($ext_table, $registry_id), $this->Registry_Model->get_primary_key());
            // ext data merge with params
            if (!array_get($registry, 'params')) {
                $registry['params'] = $ext_data;
            } elseif (is_array($registry['params'])) {
                $registry['params'] = array_merge($registry['params'], $ext_data);
            }
        }

        if (!!$post) {

            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save
                $save = $this->Registry_Model->update_by_primary_key($registry_id, [
                    'name' => $post['name'],
                    'params' => array_get($post, 'params'),
                    'status' => (int)array_get($post, 'status', \Registry_Model::STATUS_ACTIVE),
                ]);

                if ($save) {

                    // создаем запись в связанной таблице (ext_table)
                    // добавляются поля из ext_table_fields и registry_id
                    if (array_get($handbk, 'params.ext_table') && !!($ext_fields = array_get($handbk, 'params.ext_table_fields')) && is_array($ext_fields)) {

                        $ext_data = [];

                        foreach ($ext_fields as $_field) {
                            $ext_data[$_field] = array_get($post, 'params.' . $_field);
                        }

                        if ($ext_data) {
                            $this->Registry_Model->update_by_id($handbk['params']['ext_table'], $registry_id, $ext_data);
                        }
                    }

                    redirect('/admin/registry/');
                } else {
                    $errors[] = 'Не удалось изменить запись.';
                }
            }
        }

        $this->set_breadcrumb(array_get($handbk, 'name') . '. ' . array_get($registry, 'name'));

        $data = [
            'title' => $this->title = array_get($handbk, 'name') . '. Редактирование записи "' . array_get($registry, 'name') . '"',
            'content' => $this->load->view($this->template_dir . 'forms/registry', [
                'post' => $registry,
                'handbk' => $handbk,
                'status_list' => $this->Registry_Model->get_status_list(),
                    ], TRUE),
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this
                ->render($this->load->view($this->template_dir . 'pages/post_card', $data, TRUE));
    }

    public function delete() {
        // only for post ajax
        if (!$this->input->is_ajax_request() || !($post = $this->input->post()))
            show_404();

        $res = ['success' => FALSE];

        if (!($registry_id = (int) array_get($post, 'registry_id')) || !($this->Registry_Model->search(['registry_id' => $registry_id, 'status' => \Registry_Model::STATUS_ACTIVE], TRUE))) {
            $res['error'] = 'Объект не найден';
            echo json_encode($res);
            return;
        }

        $this->Registry_Model->delete_by_primary_key($registry_id);

        $res['success'] = !$this->Registry_Model->search(['registry_id' => $registry_id, 'status' => \Registry_Model::STATUS_ACTIVE], TRUE);
        echo json_encode($res);
        exit();
    }

}
