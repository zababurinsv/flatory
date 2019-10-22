<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * general_handbks controller
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Ghandbks extends MY_Controller {

    /**
     *
     * @var \Handbks_Model 
     */
    public $Handbks_Model;
    private $table_name = 'registry';

    public function __construct() {
        parent::__construct();

        $this->load->model('Handbks_Model');
    }

    public function index() {
        $this->title = 'Общие справочники';

        $get = $this->input->get(NULL, TRUE);

        $list = $this->Handbks_Model->search(array_merge($get, array(
            'with' => ['handbks_groups'],
            'table' => $this->table_name,
            'order' => array_get($get, 'sort_by', FALSE),
            'offset' => (int) array_get($get, 'per_page'),
            'order_direction' => array_get($get, 'sort_direction', FALSE),
        )));
        $total_rows = $this->Handbks_Model->found_rows();
        
        // pagination
        $this->pagination->initialize(array(
            'base_url' => $this->path . '/?' . http_build_query(array_except($get, 'per_page')),
            'total_rows' => $total_rows,
            'per_page' => $this->Handbks_Model->limit,
            'page_query_string' => TRUE,
        ));
        $pagination = $this->pagination->create_links();
        if ($pagination) {
            $get['per_page'] = -1;
            $pagination .= anchor($this->path . '/?' . http_build_query($get), 'Все ' . $total_rows, array('class' => 'pagination_btn pull-right '));
        }

        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(
            'columns' => [
                'handbk_id' => ['title' => 'ID'],
                'name' => ['title' => 'Название',],
                'group_name' => ['title' => 'Группа',],
            ],
            'list' => $list,
            'pagination' => $pagination,
            'btn_nav' => html_tag('a', ['class' => 'btn btn-default', 'href' => '/admin/ghandbks/add/'], 'Добавить'),
                ), TRUE);

        $this->render();
    }

    public function add() {
        $errors = [];
        $post = $this->input->post();

        if (!!$post) {
            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save
                $id = $this->Handbks_Model->insert([
                    'name' => $post['name'],
                    'alias' => transliteration($post['name']),
                    'table' => $this->table_name
                ]);

                if ($id)
                    redirect('/admin/ghandbks/');
                else
                    $errors[] = 'Не удалось создать справочник.';
            }
        }

        $this->title = 'Добавление общего справочника';
        $this->content = $this->load->view($this->template_dir . 'forms/ghandbks', [], TRUE);

        if (!empty($errors))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this->render();
    }

}
