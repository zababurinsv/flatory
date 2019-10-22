<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Handbk controller
 * @date 29.07.2014
 * @todo rewrite this
 */
class Handbk extends MY_Controller {

    public $uri_path = '/admin/handbk/';
    public $handbks;

    /**
     *
     * @var \M_handbk 
     */
    public $M_handbk;

    /**
     * File_Categories model
     * @var \File_Categories
     */
    public $File_Categories;

    /**
     *
     * @var \District_Model 
     */
    public $District_Model;

    /**
     *
     * @var \Geo_Area_Model 
     */
    public $Geo_Area_Model;

    /**
     *
     * @var \Populated_Locality_Model 
     */
    public $Populated_Locality_Model;

    /**
     *
     * @var \Square_Model 
     */
    public $Square_Model;

    /**
     *
     * @var \Tags_Model 
     */
    public $Tags_Model;

    /**
     *
     * @var \Metro_Line_Model 
     */
    public $Metro_Line_Model;

    /**
     *
     * @var \Metro_Station_Model 
     */
    public $Metro_Station_Model;

    public function __construct() {
        parent::__construct();
        session_start();
        if (!isset($_SESSION['login_ok'])) {
            redirect('/admin/login');
        }

        $this->load->model('Geo');
//        $this->load->model('M_handbk');
        $this->load->model('Storage_Files');
        $this->load->model('File_Categories');
        $this->load->model('Image_Albums');
        $this->load->model('District_Model');
        $this->load->model('Geo_Area_Model');
        $this->load->model('Populated_Locality_Model');
        $this->load->model('Square_Model');
        $this->load->model('Tags_Model');
        $this->load->model('Metro_Line_Model');
        $this->load->model('Metro_Station_Model');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->handbks = $this->M_handbk->handbks;

        $this->set_breadcrumb($this->title = 'Справочники', '/admin/handbk/');
    }

    /**
     * Список справочников
     */
    public function index() {
        $this->title = 'Справочники';

        $nav_list = array(
            'uri' => $this->uri_path . 'register/',
            'nav' => $this->handbks,
        );
        $this->content = $this->load->view($this->template_dir . 'handbk_nav_list', $nav_list, TRUE);
        $this->render();
    }

    /**
     * Список елементов справочника
     */
    public function register() {

//        var_dump('fffff');
        $get = xss_clean($_GET);
        $hb = $this->uri->segment(4);

        if (!$hb || !array_key_exists($hb, $this->handbks))
            redirect($this->uri_path);

        // get & check category
        if (!($category = $this->File_Categories->get_by_field('prefix', $hb, TRUE)) && !($this->handbks[$hb]))
            show_404();

        $this->title = array_get($category, 'name', $this->handbks[$hb]);

        $sort = [
            'sort_by' => $get['sort_by'] = array_get($get, 'sort_by', 'name'),
            'sort_direction' => $get['sort_direction'] = array_get($get, 'sort_direction', 'asc')
        ];

        $method = $hb . '_list';
        $data = method_exists($this, $method) ? $this->$method($hb, $get) : array();
        $data['uri'] = $this->uri_path . 'edit';
        $data['hb'] = $hb;
        $data['sort'] = $sort;

        if (!array_get($data, 'status_list'))
            $data['status_list'] = [
                MY_Model::STATUS_ACTIVE => ['alias' => 'active', 'title' => 'Опубликовано'],
                MY_Model::STATUS_NOT_PUBLISHED => ['alias' => 'not-published', 'title' => 'Черновик'],
            ];

        if (in_array($hb, ['geo_area', 'populated_locality', 'district', 'square', 'metro_line', 'metro_station', 'populated_locality_type', 'tag'])) {
            $data['list_fields']['delete'] = ['data' => [
                    'object_type' => $hb,
                    'id' => function (array $it) use($hb) {
                        return array_get($it, $hb . '_id');
                    },
                ]
            ];
        }

//       vdump($hb,1);
//       vdump($data['list_fields']);

        $this->session->set_userdata(array('register_uri' => '?' . http_build_query($get)));

        unset($get['per_page']);
        $total_rows = element('total_rows', $data, $this->Geo->count);
        $limit = element('limit', $data, $this->Geo->limit);
        // pagination
        $this->pagination->initialize(array(
            'base_url' => $this->uri_path . 'register/' . $hb . '?' . http_build_query($get),
            'total_rows' => $total_rows,
            'per_page' => $limit,
            'page_query_string' => TRUE,
//            'uri_segment' => 5,
        ));

        $data['pagination'] = $this->pagination->create_links();

        $this->content = $this->load->view($this->template_dir . 'handbk_list', $data, TRUE);
        $this
                ->set_breadcrumb($this->title)
                ->render();
    }

    /**
     * Редактирование карточки
     */
    public function edit() {
        $post = xss_clean($_POST);
        $hb = $this->uri->segment(4);
        $id = $this->uri->segment(5);

        if (!$hb || !array_key_exists($hb, $this->handbks) || !$id)
            redirect($this->uri_path);

        // get & check category
        if (!($category = $this->File_Categories->get_by_field('prefix', $hb, TRUE)) && !($this->handbks[$hb]))
            show_404();

        $method = $hb . '_edit';
        $current = method_exists($this, $method) ? $this->$method($id, $post) : array();

        $this->title = array_get($current, 'content') && is_array($current['content']) ? array_get($current['content'], 'name', 'Редактирование') : 'Редактирование';

        $this
                ->set_breadcrumb(array_get($category, 'name', $this->handbks[$hb]), '/admin/handbk/register/' . $hb . '/')
                ->set_breadcrumb($this->title);

        $data = [
            'title' => $this->title,
            'breadcrumbs' => $this->get_breadcrumb(),
            'content' => array_get($current, 'form'),
        ];

        if (isset($current['errors']))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', $current, TRUE);

        $this->render($this->load->view($this->template_dir . 'pages/handbk_card', $data, TRUE));
    }

    /**
     * Добавление в справочник
     */
    public function add() {
        $post = xss_clean($_POST);
        $hb = $this->uri->segment(4);

        if (!$hb || !array_key_exists($hb, $this->handbks))
            redirect($this->uri_path);

        $this->title = 'Справочники: ' . $this->handbks[$hb] . ' (добавление карточки)';

        $method = $hb . '_add';
        $current = method_exists($this, $method) ? $this->$method($post) : array();

        $data = [
            'title' => $this->title,
            'breadcrumbs' => $this->get_breadcrumb(),
            'content' => array_get($current, 'form'),
        ];

        if (isset($current['errors']))
            $this->after_body = $this->load->view($this->template_dir . 'errors_form', $current, TRUE);

        $this->render($this->load->view($this->template_dir . 'pages/handbk_card', $data, TRUE));
    }

    /**
     * Список Районов / Гор. округов (не МСК!)
     * @param string $hb
     * @param array $get
     * @return array
     */
    private function geo_area_list($hb, $get) {

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));

        // $this->M_handbk->form_el_zone($get) 
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => $this->M_handbk->form_el_name_like($get, $hb) . $this->load->view($this->template_dir . 'elements/status_filter', [
                'status_list' => $this->Geo_Area_Model->get_status_list(),
                'data' => $get,], TRUE),
            'col_2' => $this->M_handbk->form_el_geo_direction($get),
                ], TRUE);
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => '',
            'col_2' => $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right'),
                ], TRUE);
        $filter .= form_close();
        $list = $this->Geo_Area_Model->search(array_merge($get, [
            'with' => ['found_rows', 'geo_direction'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'total_rows' => $this->Geo_Area_Model->found_rows(),
            'form' => $filter,
            'list' => $list,
            'limit' => $limit,
            'list_fields' => array('geo_area_id' => 'ID', 'status' => 'Статус', 'name' => 'Название', 'geo_direction' => 'Направление',),
        );

        return $data;
    }

    /**
     * Редактирование карточки - Районов / Гор. округов (не МСК!)
     * @param int $id
     * @return array
     */
    private function geo_area_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
//            $this->form_validation->set_fields(array('name' => 'Название',));
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_geo_area($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    /**
     * Добавление карточки - Районов / Гор. округов (не МСК!)
     * @param array $post
     * @return array
     */
    public function geo_area_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
//            $this->form_validation->set_fields(array('name' => 'Название',));
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
//               save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_geo_area($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    /**
     * 
     * @param type $hb
     * @param type $get
     * @return type
     */
    public function populated_locality_list($hb, $get) {

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));

        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => $this->M_handbk->form_el_name_like($get, $hb) .
                $this->load->view($this->template_dir . 'elements/status_filter', ['status_list' => $this->Geo_Area_Model->get_status_list(), 'data' => $get,], TRUE),
            'col_2' => $this->M_handbk->form_el_geo_direction($get) . $this->M_handbk->form_el_geo_area($get, array_get($get, 'zone_id'), array_get($get, 'geo_direction_id')),
                ], TRUE);
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => '',
            'col_2' => $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right'),
                ], TRUE);
        // select zone_id
        // $filter .= $this->M_handbk->form_el_zone($get);
        $filter .= form_close();

        $list = $this->Populated_Locality_Model->search(array_merge($get, [
            'with' => ['found_rows', 'populated_locality_type', 'geo_area', 'geo_direction'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'total_rows' => $this->Populated_Locality_Model->found_rows(),
            'form' => $filter,
            'list' => $list,
            'limit' => $limit,
            'list_fields' => array(
                'populated_locality_id' => 'ID',
                'status' => 'Статус',
                'name' => 'Название',
                'populated_locality_type' => 'Тип населенного пункта',
                'geo_area' => 'Район / Гор. округ',
                'geo_direction' => 'Направление',
            ),
        );

        return $data;
    }

    private function populated_locality_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
//            $this->form_validation->set_fields(array('name' => 'Название',));
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_populated_locality($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function populated_locality_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
//            $this->form_validation->set_fields(array('name' => 'Название',));
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_populated_locality($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function populated_locality_type_list($hb, $get) {

        $offset = (int) element('per_page', $get, 0);
        $status = element('status', $get, '');

        $filter = '';
//        $filter .= form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
//        // status
//        $filter .= $this->M_handbk->form_el_status($get, TRUE);
//        // submit
//        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
//        $filter .= form_close();

        $order = element('sort_by', $get, FALSE);
        $order_direction = $this->M_handbk->check_order_direction(element('sort_direction', $get, FALSE));

        $list = $this->Geo->get_populated_locality_type($status, $offset, $order, $order_direction);

        $data = array(
            'form' => $filter,
            'list' => $list,
            'list_fields' => array($hb . '_id' => 'ID', 'status' => 'Статус', 'name' => 'Название', 'short_name' => 'Сокращение',),
        );

        return $data;
    }

    public function populated_locality_type_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('short_name', 'Сокращение', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_populated_locality_type($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function populated_locality_type_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('short_name', 'Сокращение', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_populated_locality_type($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function district_list($hb, $get) {

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
        
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => $this->M_handbk->form_el_name_like($get, $hb),
            'col_2' => $this->load->view($this->template_dir . 'elements/status_filter', ['status_list' => $this->Geo_Area_Model->get_status_list(), 'data' => $get,], TRUE),
                ], TRUE);
        
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => '',
            'col_2' => $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right'),
                ], TRUE);

        $filter .= form_close();

        $list = $this->District_Model->search(array_merge($get, [
            'with' => ['found_rows'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'total_rows' => $this->District_Model->found_rows(),
            'form' => $filter,
            'list' => $list,
            'limit' => $limit,
            'list_fields' => array($hb . '_id' => 'ID', 'status' => 'Статус', 'name' => 'Название', 'short_name' => 'Сокращение',),
        );

        return $data;
    }

    public function district_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('short_name', 'Сокращение', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_district($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function district_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('short_name', 'Сокращение', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_district($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function square_list($hb, $get) {

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
        
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => $this->M_handbk->form_el_name_like($get, $hb) . 
                    $this->load->view($this->template_dir . 'elements/status_filter', ['status_list' => $this->Geo_Area_Model->get_status_list(), 'data' => $get,], TRUE),
            'col_2' => $this->M_handbk->form_el_district($get),
                ], TRUE);
        
        $filter .= $this->load->view($this->template_dir . 'elements/row_col_2', [
            'col_1' => '',
            'col_2' => $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right'),
                ], TRUE);

        $filter .= form_close();

        $list = $this->Square_Model->search(array_merge($get, [
            'with' => ['found_rows', 'district'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'total_rows' => $this->Square_Model->found_rows(),
            'form' => $filter,
            'list' => $list,
            'limit' => $limit,
            'list_fields' => array($hb . '_id' => 'ID', 'status' => 'Статус', 'name' => 'Название', 'district' => 'Округ',),
        );

        return $data;
    }

    public function square_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('district_id', 'Округ', 'is_natural_no_zero');
            $this->form_validation->set_message('is_natural_no_zero', 'Нужно указать %s');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_square($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function square_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('district_id', 'Округ', 'is_natural_no_zero');
            $this->form_validation->set_message('is_natural_no_zero', 'Нужно указать %s');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_square($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function metro_line_list($hb, $get) {

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
        $filter .= $this->M_handbk->form_el_name_like($get, $hb);
        $filter .= $this->M_handbk->form_el_status($get, TRUE);
        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
        $filter .= form_close();

        $list = $this->Metro_Line_Model->search(array_merge($get, [
            'with' => ['found_rows'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'form' => $filter,
            'list' => $list,
            'total_rows' => $this->Metro_Line_Model->found_rows(),
            'limit' => $limit,
            'list_fields' => array($this->Metro_Line_Model->get_primary_key() => 'ID', 'status' => 'Статус', 'name' => 'Название', 'color' => 'Цвет',),
        );

        return $data;
    }

    public function metro_line_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('color', 'Цвет', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->Geo->insert($hb, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }
        // require colorpicker
        $this->styles[] = 'bootstrap-colorpicker.min.css';
        $this->scripts[] = 'bootstrap-colorpicker.min.js';

        $form = $this->M_handbk->form_metro_line($post);
        $form .= $this->M_handbk->form_colorpicker();

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function metro_line_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('color', 'Цвет', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data  
                $save = $this->Geo->update_by_id($hb, $id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }
        // require colorpicker
        $this->styles[] = 'bootstrap-colorpicker.min.css';
        $this->scripts[] = 'bootstrap-colorpicker.min.js';

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_metro_line($content);
        $form .= $this->M_handbk->form_colorpicker();

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function metro_station_list($hb, $get) {
        $offset = (int) element('per_page', $get, 0);
        $metro_line_id = element('metro_line_id', $get, '');
        $status = element('status', $get, '');

        $filter = form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
        $filter .= $this->M_handbk->form_el_name_like($get, $hb);
        $filter .= $this->M_handbk->form_el_metro_line($get);
        $filter .= $this->M_handbk->form_el_status($get, TRUE);
        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
        $filter .= form_close();

        $order = element('sort_by', $get, FALSE);
        $order_direction = $this->M_handbk->check_order_direction(element('sort_direction', $get, FALSE));

        $list = $this->Geo->get_metro_station($metro_line_id, $status, $offset, $order, $order_direction, $limit = $this->pagination->get_pagination_limit(), $get);
//        vdump($list);
        $data = array(
            'form' => $filter,
            'list' => $list,
            'total_rows' => $this->Geo->count,
            'limit' => $limit,
            'list_fields' => array(
                $this->Metro_Station_Model->get_primary_key() => 'ID',
                'status' => 'Статус',
                'name' => 'Название',
                'metro_lines' => 'Линия метро',
                'color' => 'Цвет',
            ),
        );

        return $data;
    }

    public function metro_station_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('metro_line_id[]', 'Линия метро', 'is_natural_no_zero');
            $this->form_validation->set_message('is_natural_no_zero', 'Поле "%s" должно быть заполненно.');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $this->load->model('Metro_Station_Model');
                $save = $this->Metro_Station_Model->insert($post);
                if ($save)
                    redirect($this->uri_path . 'edit/metro_station/' . $save);
            }
        }

        $form = $this->M_handbk->form_metro_station($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function metro_station_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!!$post) {

            $this->_metro_station_edit_params($id, $post);

            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('metro_line_id[]', 'Линия метро', 'is_natural_no_zero');
            $this->form_validation->set_message('is_natural_no_zero', 'Поле "%s" должно быть заполненно.');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data
                $this->load->model('Metro_Station_Model');
                $save = $this->Metro_Station_Model->update_by_primary_key($id, $post);

                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Geo->get_by_id($hb, (int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_metro_station($content);
        $form .= $this->load->view($this->template_dir . 'pages/metro_station_edit', ['metro_station' => $content], true);
        $this->set_scripts_bottom('/js/metro.js');

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    /**
     * update metro_station.params (Ajax)
     * @param int $id - metro_station_id
     * @param array $post - post request
     * @return null
     */
    private function _metro_station_edit_params($id, array $post) {
        if (!$this->input->is_ajax_request())
            return;

        $errors = $params = [];
        $required_fields = ['marker', 'points'];

        foreach ($required_fields as $field) {
            if (!array_get($post, $field))
                $errors[$field] = 'Field "' . $field . '" not found!';
            else
                $params[$field] = $post[$field];
        }

        if (!$errors) {
            $save = $this->Geo->update_by_id('metro_station', $id, [
                'params' => json_encode($params)
            ]);
        }

        echo json_encode(['success' => !$errors, 'data' => isset($save) ? $save : Null, 'errors' => $errors, 'post' => $post]);
        exit();
    }

    public function proportion_list($hb, $get) {
        $offset = (int) element('per_page', $get, 0);
        $status = element('status', $get, '');

//        $filter = '';
//        $filter .= form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
//        // status
//        $filter .= $this->M_handbk->form_el_status($get, TRUE);
//        // submit
//        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
//        $filter .= form_close();

        $order = element('sort_by', $get, FALSE);
        $order_direction = $this->M_handbk->check_order_direction(element('sort_direction', $get, FALSE));

        $list = $this->Storage_Files->get_proportions($status, $offset, $order, $order_direction);
        $data = array(
            'form' => $filter,
            'list' => $list,
            'list_fields' => array($hb . '_id' => 'ID', 'name' => 'Название', 'x' => 'Ширина (px)', 'y' => 'Высота (px)', 'status' => 'Статус'),
        );
        return $data;
    }

    public function proportion_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        $this->load->model('Proportions');

        if (!empty($post)) {
            $this->form_validation->set_rules('x', 'Ширина', 'required');
            $this->form_validation->set_rules('x', 'Ширина', 'numeric');
            $this->form_validation->set_rules('y', 'Высота', 'required');
            $this->form_validation->set_rules('y', 'Высота', 'numeric');
//            $this->form_validation->set_message('is_natural_no_zero', 'Поле "%s" должно быть больше нуля.');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                $post['name'] = element('x', $post, '') . 'x' . element('y', $post, '');
                //  save data  
                $save = $this->Proportions->create($post);
                if ($save) {
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
                }
            }
        }

        $form = $this->M_handbk->form_proportions($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function proportion_edit($id, $post = array()) {
//        $hb = str_replace('_edit', '', __FUNCTION__);
//
//        if (!empty($post)) {
//            $this->form_validation->set_rules('name', 'Название', 'required');
//            $this->form_validation->set_rules('x', 'Ширина', 'required');
//            $this->form_validation->set_rules('x', 'Ширина', 'numeric');
//            $this->form_validation->set_rules('y', 'Высота', 'required');
//            $this->form_validation->set_rules('y', 'Высота', 'numeric');
//            if (!$this->form_validation->run()) {
//                $errors = $this->form_validation->get_errors();
//            } else {
//                // save data
//                $save = $this->Storage_Files->update_by_id($hb .'s', $id, $post);
//                if ($save)
//                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
//            }
//        }
//
//        $content = $this->Storage_Files->get_by_id($hb . 's', (int) $id);
//        if (isset($post))
//            $content = array_merge($content, $post);
//
//        $form = $this->M_handbk->form_proportions($content);
//
//        $data = array(
//            'content' => $content,
//            'form' => $form,
//            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
//        );


        $data = array('form' => 'Пропорции запрешены к редактированию.');
        return $data;
    }

    public function file_catigories_list($hb, $get) {
        $this->load->model('File_Categories');
        $offset = (int) element('per_page', $get, 0);
        $status = element('status', $get, '');

//        $filter = '';
//        $filter .= form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
//        // status
//        $filter .= $this->M_handbk->form_el_status($get, TRUE);
//        // submit
//        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
//        $filter .= form_close();

        $order = element('sort_by', $get, FALSE);
        $order_direction = $this->M_handbk->check_order_direction(element('sort_direction', $get, FALSE));


        $list = $this->File_Categories->get_list($status, $offset, $order, $order_direction);

        $data = array(
            'form' => $filter,
            'list' => $list,
            'total_rows' => $this->File_Categories->found_rows(),
            'list_fields' => array($this->File_Categories->get_primary_key() => 'ID', 'name' => 'Название', 'prefix' => 'Префикс', 'uri' => 'Ссылка', 'uri_adm' => 'Ссылка в админке', 'status' => 'Статус'),
        );
        return $data;
    }

    public function file_catigories_add($post = array()) {
        $this->load->model('File_Categories');
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('prefix', 'Префикс', 'required');
            $this->form_validation->set_rules('uri', 'Ссылка', 'required');
            $this->form_validation->set_rules('uri_adm', 'Ссылка в админке', 'required');

//            $this->form_validation->set_message('is_natural_no_zero', 'Поле "%s" должно быть больше нуля.');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data  
                $save = $this->File_Categories->insert($post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_file_catigory($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function file_catigories_edit($id, $post = array()) {
        $this->load->model('File_Categories');
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            $this->form_validation->set_rules('prefix', 'Префикс', 'required');
            $this->form_validation->set_rules('uri', 'Ссылка', 'required');
            $this->form_validation->set_rules('uri_adm', 'Ссылка в админке', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data
                $save = $this->File_Categories->update_by_primary_key($id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->File_Categories->get_by_primary_key((int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_file_catigory($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );
        return $data;
    }

    public function tag_list($hb, $get) {

        $filter = '';
        $filter .= form_open('', array('method' => 'get', 'class' => 'form_filter', 'role' => 'form'), array('hb' => $hb));
        // name
        $filter .= $this->M_handbk->form_el_name_like($get, $hb);

        // submit
        $filter .= $this->M_handbk->form_el_submit('Фильтр', 'btn-primary pull-right');
        $filter .= form_close();

        $list = $this->Tags_Model->search(array_merge($get, [
            'with' => ['found_rows'],
            'offset' => (int) array_get($get, 'per_page'),
            'order' => array_get($get, 'sort_by', 'name'),
            'order_direction' => array_get($get, 'sort_direction', 'asc'),
            'limit' => $limit = $this->pagination->get_pagination_limit()
        ]));

        $data = array(
            'form' => $filter,
            'list' => $list,
            'total_rows' => $this->Tags_Model->found_rows(),
            'limit' => $limit,
            'list_fields' => array($this->Tags_Model->get_primary_key() => 'ID', 'name' => 'Название', 'alias' => 'Алиас',),
        );
        return $data;
    }

    public function tag_add($post = array()) {
        $hb = str_replace('_add', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                //  save data
                $save = $this->Tags_Model->insert($post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $form = $this->M_handbk->form_tag($post);

        $data = array(
            'content' => $post,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );

        return $data;
    }

    public function tag_edit($id, $post = array()) {
        $hb = str_replace('_edit', '', __FUNCTION__);

        if (!empty($post)) {
            $this->form_validation->set_rules('name', 'Название', 'required');
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // save data
                $save = $this->Tags_Model->update_by_primary_key($id, $post);
                if ($save)
                    redirect($this->uri_path . 'register/' . $hb . $this->session->userdata('register_uri'));
            }
        }

        $content = $this->Tags_Model->get_by_primary_key((int) $id);
        if (isset($post))
            $content = array_merge($content, $post);

        $form = $this->M_handbk->form_tag($content);

        $data = array(
            'content' => $content,
            'form' => $form,
            'errors' => isset($errors) ? json_encode($errors) : json_encode(array()),
        );
        return $data;
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
        $this->bottom_scripts[] = 'object_cart.js';

        $this->after_body = $this->load->view($this->template_dir . 'errors_form', array('errors' => $errors), TRUE);
        return $content;
    }

}
