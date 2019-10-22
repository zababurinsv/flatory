<?php

class Objects extends MY_Controller {

    /**
     * Model storage
     * @var Storage_Files 
     */
    public $Storage_Files;

    /**
     * Model
     * @var \File_Categories 
     */
    public $File_Categories;

    /**
     * Model Links
     * @var \Links 
     */
    public $Links;

    /**
     * model Object_Model
     * @var \Object_Model 
     */
    public $Object_Model;

    /**
     * model Geo
     * @var \Geo 
     */
    public $Geo;

    /**
     * validation
     * @var \Form_validation 
     */
    public $form_validation;

    /**
     * Tags_Model
     * @var \Tags_Model 
     */
    public $Tags_Model;

    /**
     * no init
     * @var \Registry_Model 
     */
    public $Registry_Model;

    /**
     * no init
     * @var \Handbks_Model
     */
    public $Handbks_Model;

    /**
     *
     * @var \Object_Section_Model 
     */
    public $Object_Section_Model;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
//        session_start();
        if (!isset($_SESSION['login_ok'])) {
            redirect('/admin/login');
        }
        $this->load->model('Storage_Files');
        $this->load->model('File_Categories');
        $this->load->model('Links');
        $this->load->model('Object_Model');
        $this->load->model('Geo');
        $this->load->model('Flats');
        $this->load->model('Tags_Model');
        $this->load->model('Handbks_Model');
        $this->load->model('Object_Section_Model');
        $this->load->model('Registry_Model');

        $this->load->library('form_validation');

        $data['object_id'] = $object_id = $this->uri->segment(4);
        if ($object_id == '') {
            $_SESSION['status_object'] = 'add';
            $_SESSION['object_name'] = '';
        } else {
            $status = $this->db->where('id', $object_id)->get('main_object')->row()->status;
            if ($status == 0) {
                $_SESSION['status_object'] = 'add';
            } else {
                $_SESSION['status_object'] = 'edit';
            }
            $_SESSION['object_name'] = $this->db->where('id', $object_id)->get('main_object')->row()->name;
        }
        if (count($this->uri->segment_array()) == 3 && $this->uri->segment(3) == 'find_icon_metro') {
            return;
        }

        $this->path = '/admin/objects/';
        $this->set_breadcrumb($this->title = 'Каталог', $this->path);
    }

    public function get_method() {
        $data = get_class_methods('Objects');
        return $data;
    }

    public function index() {
//        var_dump('ddddd');die;

        $get = $this->input->get(NULL, TRUE);


        $sort = array(
            'sort_by' => array_get($get, 'sort_by', 'id'),
            'sort_direction' => array_get($get, 'sort_direction', 'desc')
        );
        if(count($get,COUNT_NORMAL) == 0){

        }else{
            $list = $this->Object_Model->search(array_merge($get, array(
                'with' => array('created', 'updated', 'status'),
                'order' => $sort['sort_by'],
                'offset' => (int) array_get($get, 'per_page'),
                'limit' => $limit = $this->pagination->get_pagination_limit(),
                'order_direction' => $sort['sort_direction'],
            )));
        }
        $total_rows = $this->Object_Model->found_rows();

        // pagination
        $pagination = $this->pagination->initialize(array(
                    'base_url' => $this->path . '?' . http_build_query(array_except($get, 'per_page')),
                    'total_rows' => $total_rows,
                    'per_page' => $limit,
                    'page_query_string' => TRUE,
                ))->create_links();


        $tags = $this->Tags_Model->get_tags();
        $status_list = $this->Object_Model->get_status_list();
//        echo'<pre>';
//        var_dump($this->Object_Model);die;
        $this->content = $this->load->view($this->template_dir . 'pages/content_table', array(

            'filters' => $this->load->view($this->template_dir . 'filters/objects', array(
                'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
                'status' => $status_list,
            ), TRUE),
            'columns' => array(
                'id' => array('title' => 'ID'),
                'status' => array('title' => 'Статус',),
                'name' => array('title' => 'Название',),
                'created' => array('title' => 'Дата создания', 'decorate' => 'date'),
                'updated' => array('title' => 'Дата изменения', 'decorate' => 'date'),
                'delete' => array('title' => '', 'decorate' => '', 'data' => array(
                        'object_type' => 'object',
                        'id' => function (array $it) {
                            return array_get($it, 'id');
                        },
                )
                ),
            ),
            'path_table_row_template' => $this->template_dir . 'elements/object_table_row',
            'list' => $list,
            'content_vars' => array(
                'status_list' => $status_list,
            ),
            'pagination' => $pagination,
            'sort' => $sort,
            'btn_nav' => $this->load->view($this->template_dir . 'navs/btn_nav', array(
                'list' => array(
                    'create' => array(
                        'url' => $this->path . 'general_info/',
                        'title' => 'Добавить',
                        'glyphicon' => 'glyphicon-plus',
                        'type' => 'info',
                    )
                ),
            ), TRUE)
                ), TRUE);

//        $this->set_scripts_bottom('/js/main-table.js')->render();
        $this->set_scripts_bottom('/js/modules/object_list.js')->render();

    }

    /**
     * Описание
     * @param int $object_id
     * @todo get $category_files
     */
    public function general_info($object_id = 0) {
        $this->title = 'Описание';

        $file_category = $this->File_Categories->get_by_field('prefix', 'catalog');
        $file_category_id = array_get($file_category, 'file_category_id');
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => array('images', 'upload')));
        $object = $this->db->where('id', $object_id)->get('main_object')->row_array();

        $errors = !!($post = $this->input->post()) ? $this->_general_info_post($post, $object, $file_category_id) : [];

        $data = array(
            'object_id' => $object_id = (int) $object_id,
            'object' => is_array($post) && !!$post ? array_merge($object, $post) : $object,
            'status_list' => $this->Object_Model->get_status_list(),
            'view_images' => $this->load->view($this->template_dir . 'widgets/tile_galery_images', array(
                'list_class' => 'tile_handles',
                'images' => $this->Storage_Files->get_by_category($file_category_id, $object_id),
                'file_category_id' => $file_category_id,
            ), TRUE),
            'widget_storage' => $this->widget_storage->render(),
        );

        $object_tags = $this->Object_Model->get_tags($object_id);
        $data['object']['tags'] = implode('|', $object_tags);

        if (isset($object['name']))
            $this->set_breadcrumb($this->title = $object['name']);
        
        $content = array(
            'action' => __FUNCTION__,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/general_info', $data, TRUE)
        );


        // добавляем разделы только когда есть объект
        if ($object) {
            $content['sections'] = simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => (int) $object_id)), 'alias');
            $content['current_section'] = __FUNCTION__;
        }

//        var_dump($this->template_dir);die;
        if (!empty($errors))
            $this->after_body .= $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);
            $this
                ->set_html_tpls($this->load->view($this->template_dir . 'html_tpls/card__item_view__tile', array(), TRUE))
                ->set_scripts(array(
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(array('/js/' . $this->template_dir . 'objectcard.js', '/js/object_cart.js'))
                ->set_breadcrumb($this->title)
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    /**
     * post general_info
     * @param array $post
     * @param array $object
     * @param int $file_category_id
     * @return array
     */
    private function _general_info_post(array $post, array $object, $file_category_id) {

//        var_dump($file_category_id);
//        var_dump($object);
//        var_dump($post);die;
        $errors = array();

        $this->form_validation->set_rules('name', 'Название', 'required');
        $this->form_validation->set_rules('alias', 'Алиас', 'required|alias');

        if (!$this->form_validation->run()) {
            $errors = $this->form_validation->get_errors();
        } else {
            // no modify alias (modified on front!)
            $alias = $post['alias'];
            $object_id = (int) array_get($object, 'id');

            // check name and alias exists
            // name
            if ($this->db->where('id !=', $object_id)->where('name', $post['name'])->get('main_object')->row_array())
                $errors['name'] = 'Такое имя уже есть в системе!';
            // alias
            if ($this->db->where('id !=', $object_id)->where('alias', $alias)->get('main_object')->row_array())
                $errors['alias'] = 'Такой алиас уже есть в системе!';

            // no errors - save & redirect
            if (!$errors) {

                // save general info (main_object table)
                $general_info = array(
                    'name' => $post['name'],
                    'alias' => $alias,
                    'description' => array_get($post, 'description'),
                    'anons' => array_get($post, 'anons'),
                    'status' => (int) array_get($post, 'status', Object_Model::STATUS_NOT_PUBLISHED),
                );



                if (!$object_id) {
                    $this->db->insert('main_object', array_merge($general_info, array('created' => date('Y-m-d H:i:s'))));
                    $object_id = $this->db->insert_id();

//                    var_dump($object_id);die;
                    // create meta
                    $this->db->insert('meta', array(
                        'id_object' => $object_id,
                    ));
                } else {
                    $this->db->where('id', $object_id)->update('main_object', $general_info);
                }

                // set files involves
                if (!!$object) {
                    $this->Storage_Files->set_files_involves($file_category_id, $object_id, $alias, array_unique(array_get($post, 'files', [])), array_get($post, 'sort', []));
                }

                // save tags
                $tags = explode('|', array_get($post, 'tags', ''));
                $tags = $this->Tags_Model->update_tags($tags);
                $this->Object_Model->set_tags($object_id, $tags);

                redirect('/admin/objects/general_info/' . $object_id);
            }
        }

        return $errors;
    }

    /**
     * 
     * @deprecated 
     * @param type $object_id
     */
    public function _object_location($object_id = '') {

        // load model Registry_Model
        $this->load->model('Registry_Model');

        /**
         * Получаем данные для селектов
         */
        $data = array(
            'zone' => $this->Geo->zone,
            'geo_area' => $this->Geo->get_geo_area($this->Geo->zone['MOS']->zone_id),
            'direction' => $this->Geo->get_directions(),
            'district' => $this->Geo->get_locality('MOW'),
            'metro' => $this->Geo->get_metro_station(),
            'direction' => $this->db->get('direction')->result(),
            'distance_to_mkad' => $this->db->get('distance_to_mkad')->result(),
            'object_id' => $object_id,
        );

        $tmp_object = $this->db->select('id_object')->where('id_object', $object_id)->get('meta')->row_array();

        $post = xss_clean($_POST);

        if (!empty($post)) {

//            vdump($post);
            // получаем все станции метро объекта вместе с удаленностью, пешком и на авто
            $metro = element('metro_staition', $post, array());

            $this->db->where('object_id', $object_id)->delete('meta_metro');
            $this->db->where('id_object', $object_id)->delete('distance_to_metro');

            $metro_ids = array();
            foreach ($metro as $item) {

                if ($item['id'] != 0) {
                    $this->db->insert('distance_to_metro', array(
                        'id_object' => $object_id,
                        'id_metro' => $item['id'],
                        'name' => $item['distance'],
                        'min' => $item['distance_foot'],
                        'car' => $item['distance_car']
                    ));
                    $this->db->insert('meta_metro', array(
                        'object_id' => $object_id,
                        'metro_id' => $item['id']
                    ));
                    $metro_ids[] = $item['id'];
                }
            }

            $object_meta = array(
                'zone_id' => $post['zone_id'],
                'x' => $this->input->post('x'),
                'y' => $this->input->post('y'),
                'point' => $this->input->post('point'),
                'bus' => $this->input->post('bus'),
                'auto' => $this->input->post('auto'),
            );

            if (element('geo_direction_id', $post, FALSE))
                $object_meta['geo_direction_id'] = $post['geo_direction_id'];
            // mo
            if (element('geo_area_id', $post, FALSE))
                $object_meta['geo_area_id'] = $post['geo_area_id'];
            if (element('populated_locality_id', $post, FALSE))
                $object_meta['populated_locality_id'] = $post['populated_locality_id'];
            // msk
            if (element('district_id', $post, FALSE))
                $object_meta['district_id'] = $post['district_id'];
            if (element('square_id', $post, FALSE))
                $object_meta['square_id'] = $post['square_id'];
            if (!empty($metro_ids))
                $object_meta['metro_station_ids'] = implode(',', $metro_ids);

            if ($object_id != '' && (isset($tmp_object['id_object'])) && ($tmp_object['id_object'] != '')) {
                $this->db->where('id_object', $object_id)->update('meta', $object_meta);
            } else {
                $object_meta['id_object'] = $object_id;
                $this->db->insert('meta', $object_meta);
            }

            // save registry
            if (($registry_ids = $this->input->post('registry_id')) && is_array($registry_ids)) {
                $r = [];
                foreach ($registry_ids as $registry_id) {
                    if (!!($registry_id = (int) $registry_id) && !in_array($registry_id, $r))
                        $r[] = (int) $registry_id;
                }
                // clear old registry_ids
                $this->Registry_Model->delete_object_relations_by_handbks_groups_alias(__FUNCTION__, $object_id);
                // set new registry ids
                foreach ($r as $rid)
                    $this->db->insert('registry_has_main_object', array('object_id' => $object_id, 'registry_id' => $rid));
            }

            $this->add_status($object_id, 4);
            $this->Geo->handbks_object_counts_update();
            redirect('/admin/objects/object_location/' . $object_id);
        }

        // all registry handbks for current action
        $registry = $this->Registry_Model->search(array('status' => \Registry_Model::STATUS_ACTIVE, 'with' => array('handbk_name'), 'handbks_groups_alias' => __FUNCTION__));
        $data['registry_handbks'] = $this->Registry_Model->prepare_registry_handbks($registry);
        $this->set_scripts_bottom(array('dashboardObject.js', 'form_controls.js'));
        // current registry ids
        $registry_ids = $this->db->where('object_id', $object_id)->get('registry_has_main_object')->result_array();
        if ($registry_ids)
            $data['registry_ids'] = array_keys(simple_tree($registry_ids, 'registry_id'));

        if ($object_id != '' && (isset($tmp_object['id_object'])) && ($tmp_object['id_object'] != '')) {
            $data['object_id'] = $object_id;
            $data['ids'] = $meta = $this->db->where('id_object', $object_id)->get('meta')->row_array();
            $data['route'] = array(
                'bus' => array_get($meta, 'bus', ''),
                'auto' => array_get($meta, 'auto', ''),
            );
            $data['x'] = array_get($meta, 'x', '');
            $data['y'] = array_get($meta, 'y', '');
            $data['point'] = array_get($meta, 'point', '');

            $metro_ids = '';
            $ids_metro = array_keys(simple_tree($this->db->where('object_id', $object_id)
//                        ->where('status', $ids['id_region'])
                                    ->select('metro_id')->get('meta_metro')->result_array(), 'metro_id'));

            $metro_station = array();
            if (!empty($ids_metro))
                $metro_station = $this->Geo->get_metro_station_by_ids($ids_metro);
            $distance_to_metro = simple_tree($this->db->where('id_object', $object_id)->get('distance_to_metro')->result_array(), 'id_metro');

//                vdump($metro_station, 1);
//                vdump($distance_to_metro, 1);

            $metro_data = array();
            foreach ($metro_station as $item) {
                $metro_data[] = array(
                    'name' => $item['name'],
                    'id' => $item['metro_station_id'],
                    'color' => $item['color'],
                    'distance' => isset($distance_to_metro[$item['metro_station_id']]['name']) ? $distance_to_metro[$item['metro_station_id']]['name'] : '',
                    'distance_foot' => isset($distance_to_metro[$item['metro_station_id']]['min']) ? $distance_to_metro[$item['metro_station_id']]['min'] : '',
                    'distance_car' => isset($distance_to_metro[$item['metro_station_id']]['car']) ? $distance_to_metro[$item['metro_station_id']]['car'] : '',
                );
            }

//                dump($data);
            $data['metro_data'] = $metro_data;
            $data['zone_name'] = @$this->db->where('zone_id', $meta['id_zone'])->get('zone')->row()->name;
        }
        $this->content = $this->load->view('admin/v_tab_object', $data, TRUE) . $this->load->view('admin/v_object_location', $data, TRUE);

        $this->render();
    }

    public function location($object_id = false) {

        if (!$object_id || !($object = $this->db->where('main_object.id', $object_id)->join('meta', 'meta.id_object = main_object.id', 'left')->get('main_object')->row_array()))
            show_404();

        if (($post = $this->input->post())) {
//            vdump($post, 1);

            $this->form_validation->set_rules('adres', 'Адрес', 'required');
            $this->form_validation->set_rules('zone_id', 'Регион ', 'required');

            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->get_errors();
            } else {
                // start transaction
                $this->db->trans_start();
                // если адрес изменился  - сохраняем
                $this->db->where('id', $object_id)->update('main_object', array(
                    'adres' => $post['adres']
                ));

                // meta_metro
                if (($meta_metro = array_get($post, 'meta_metro'))) {
                    // drop old
                    $this->db->where('object_id', $object_id)->delete('meta_metro');

                    $metro_ids = [];

                    foreach ($meta_metro as $it) {
                        if (!(int) array_get($it, 'metro_id') || in_array((int) $it['metro_id'], $metro_ids))
                            continue;

                        $this->db->insert('meta_metro', array(
                            'object_id' => $object_id,
                            'metro_id' => (int) $it['metro_id'],
                            'distance' => $it['distance'],
                            'walking_time' => $it['walking_time'],
                            'drive_time' => $it['drive_time'],
                        ));

                        array_push($metro_ids, (int) $it['metro_id']);
                    }
                }

                // обновляем meta 
                {
                    // поля для meta
                    $meta_fields = array('zone_id', 'district_id', 'square_id', 'geo_direction_id',
                        'populated_locality_id', 'geo_area_id', 'id_distance_to_mkad', 'x', 'y', 'panorama_ya', 'panorama_ggl');
                    $meta = array();
                    foreach ($meta_fields as $field) {
                        $meta[$field] = array_get($post, $field);
                    }

//                    vdump($meta);

                    if ($meta)
                        $this->db->where('id_object', $object_id)->update('meta', $meta);
                }

                // save registry
                if (($registry_ids = array_get($post, 'registry_id')) && is_array($registry_ids)) {
                    $r = [];
                    foreach ($registry_ids as $registry_id) {
                        if (!!($registry_id = (int) $registry_id) && !in_array($registry_id, $r))
                            $r[] = (int) $registry_id;
                    }
                    // clear old registry_ids
                    $this->Registry_Model->delete_object_relations_by_handbks_groups_alias(__FUNCTION__, $object_id);
                    // set new registry ids
                    foreach ($r as $rid)
                        $this->db->insert('registry_has_main_object', array('object_id' => $object_id, 'registry_id' => $rid));
                }

                $this->Geo->handbks_object_counts_update();
                $this->db->trans_complete();

//                vdump($this->db->trans_status() );

                redirect('/admin/objects/location/' . $object_id);
            }
        }

        $this->title = 'Местоположение';
        $this->set_breadcrumb($object['name'], '/admin/objects/general_info/' . $object_id);
        $this->set_breadcrumb($this->title);
        $this->title = $object['name'];


        // all registry handbks for current action
        $registry = $this->Registry_Model->search(array('status' => \Registry_Model::STATUS_ACTIVE, 'with' => array('handbk_name'), 'handbks_groups_alias' => __FUNCTION__));

//        vdump(!!$post ? array_merge($object, $post) : $object);

        $data = array(
            'object_id' => $object_id,
            'object' => !!$post ? array_merge($object, $post) : $object,
            'status_list' => $this->Object_Model->get_status_list(),
            'zone' => $this->Geo->zone,
            'geo_area' => $this->Geo->get_geo_area($this->Geo->zone['MOS']->zone_id),
            'direction' => $this->Geo->get_directions(),
            'district' => $this->Geo->get_locality('MOW'),
            'metro' => $this->Geo->get_metro_station(),
            'direction' => $this->db->get('direction')->result(),
            'distance_to_mkad' => $this->db->get('distance_to_mkad')->result(),
            'registry_handbks' => $this->Registry_Model->prepare_registry_handbks($registry),
            'panoram_types' => array(
                'panorama_ya' => 'Яндекс',
                'panorama_ggl' => 'Google',
            )
        );

        // current registry ids
        $registry_ids = $this->db->where('object_id', $object_id)->get('registry_has_main_object')->result_array();
        $data['registry_ids'] = !!$registry_ids ? array_keys(simple_tree($registry_ids, 'registry_id')) : [];        
        $data['registry_ids'] = !!$post ? array_merge($data['registry_ids'], array_get($post, 'registry_id', [])) : [];
        
        // current meta_metro
        $meta_metro = $this->db->where('object_id', $object_id)->join('metro_station', 'metro_station.metro_station_id = meta_metro.metro_id', 'left')->get('meta_metro')->result_array();

        $data['meta_metro'] = !!$post ? array_merge($meta_metro, array_get($post, 'meta_metro', [])) : $meta_metro;

        $content = array(
            'action' => __FUNCTION__,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/location', $data, TRUE),
            'sections' => simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => $object_id)), 'alias'),
            'current_section' => __FUNCTION__,
        );

        if (!empty($errors))
            $this->after_body .= $this->load->view($this->template_dir . 'errors_form', array('errors' => json_encode($errors)), TRUE);

        $this
                ->set_scripts('https://api-maps.yandex.ru/2.1/?lang=ru_RU' . (ENVIRONMENT !== 'production' ? '&mode=debug' : ''))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js', '/js/dashboard/object_location.js', '/js/dashboardObject.js'])
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    public function technical_characteristics($object_id = false) {

        if (!$object_id || !($object = $this->db->where('main_object.id', $object_id)->join('meta', 'meta.id_object = main_object.id', 'left')->get('main_object')->row_array()))
            show_404();

        // load model Registry_Model
        $this->load->model('Registry_Model');

        if (($post = $this->input->post())) {

            // start transaction
            $this->db->trans_start();

            // высота потолка (ceiling_height)
            {
                $ceiling_height = array_get($post, 'ceiling_height');
                // удаляем старые связи с объектами
                $this->db->where('object_id', $object_id)->delete('meta_ceiling_height');
                if (is_array($ceiling_height) && $ceiling_height) {
                    $ceiling_height = array_unique($ceiling_height);
                    foreach ($ceiling_height as $it) {

                        if (!$it)
                            continue;

                        $this->db->insert('meta_ceiling_height', ['object_id' => $object_id, 'ceiling_height' => $it]);
                    }
                }
            }

            // delivery
            if (($delivery = array_get($post, 'delivery')) && is_array($delivery)) {

                $new_delivery = array(
                    'quarter' => (int) array_get($delivery, 'quarter'),
                    'year' => (int) array_get($delivery, 'year'),
                    'quarter_start' => (int) array_get($delivery, 'quarter_start'),
                    'year_start' => (int) array_get($delivery, 'year_start'),
                );

                // upd
                if ($this->db->where('object_id', $object_id)->get('delivery')->row_array()) {
                    $this->db->where('object_id', $object_id)->update('delivery', $new_delivery);
                } else {
                    $new_delivery['object_id'] = $object_id;
                    $this->db->insert('delivery', $new_delivery);
                }
            }


            // save registry
            if (($registry_ids = $this->input->post('registry_id')) && is_array($registry_ids)) {
                $r = [];
                foreach ($registry_ids as $registry_id) {
                    if (!!($registry_id = (int) $registry_id) && !in_array($registry_id, $r))
                        $r[] = (int) $registry_id;
                }
                // rm rel registry-object by handbk & object
                $this->Registry_Model->delete_object_relations_by_handbks([\Handbks_Model::BUILDING_LOT, \Handbks_Model::TYPE_OF_BUILDING], $object_id);

                // clear old registry_ids
                $this->Registry_Model->delete_object_relations_by_handbks_groups_alias(__FUNCTION__, $object_id);
                // set new registry ids
                foreach ($r as $rid)
                    $this->db->insert('registry_has_main_object', array('object_id' => $object_id, 'registry_id' => $rid));
            }

            // get post rooms
            $rooms = $this->input->post('room');
            // save rooms (flats)
            $this->Flats->save_rooms($rooms, $object_id);

            $technical_characteristics = array(
                // 1 - Без отделки
                // 2 - С отделкой
                // 3 - С отделкой / Без отделки
                'id_furnish' => !array_get($post, 'furnish') || !is_array(array_get($post, 'furnish')) ? NULL : (in_array('yes', $post['furnish']) && in_array('no', $post['furnish']) ? 3 : (in_array('yes', $post['furnish']) ? 2 : 1) ),
                'floor_begin' => $this->input->post('floor_begin'),
                'floor_end' => $this->input->post('floor_end'),
                'number_of_sec' => $this->input->post('number_of_sec'),
                'characteristics_anons' => $this->input->post('characteristics_anons'),
            );

            $this->db->where('id_object', $object_id)->update('meta', $technical_characteristics);

            $this->db->trans_complete();

            redirect('/admin/objects/technical_characteristics/' . $object_id);
        }

        $this->title = 'Тех. характеристики';
        $this->set_breadcrumb($object['name'], '/admin/objects/general_info/' . $object_id);
        $this->set_breadcrumb($this->title);
        $this->title = $object['name'];

        $registry = simple_tree_group($this->Registry_Model->search(['object_id' => $object_id, 'status' => \Registry_Model::STATUS_ACTIVE, 'with' => ['handbk_name']]), 'handbk_id');

//        vdump($registry);
        // тип здания
        $object['building_id'] = array_keys(simple_tree(array_get($registry, \Handbks_Model::TYPE_OF_BUILDING, []), 'registry_id'));
        // серия здания
        $object['building_lot_id'] = array_keys(simple_tree(array_get($registry, \Handbks_Model::BUILDING_LOT, []), 'registry_id'));
        $object['ceiling_height'] = array_keys(simple_tree($this->db->where('object_id', $object_id)->select('ceiling_height')->get('meta_ceiling_height')->result_array(), 'ceiling_height'));
        $object['delivery'] = $this->db->where('object_id', $object_id)->get('delivery')->row_array();
        $object['room'] = $this->Flats->get_rooms_by_object_id($object_id);

        if (($id_furnish = (int) array_get($object, 'id_furnish'))) {
            switch ($id_furnish) {
                // 1 - Без отделки
                // 2 - С отделкой
                // 3 - С отделкой / Без отделки
                case 1:
                    $object['furnish'] = ['no'];
                    break;
                case 2:
                    $object['furnish'] = ['yes'];
                    break;
                case 3:
                    $object['furnish'] = ['yes', 'no'];
                    break;
            }
        }


        // current registry ids
        $registry_ids = $this->db->where('object_id', $object_id)->get('registry_has_main_object')->result_array();
        if ($registry_ids)
            $object['registry_id'] = array_keys(simple_tree($registry_ids, 'registry_id'));

        if (array_get($_GET, 't'))
            vdump($object);

        // all registry handbks for current action
        $registry = $this->Registry_Model->search(['status' => \Registry_Model::STATUS_ACTIVE, 'with' => ['handbk_name'], 'handbks_groups_alias' => __FUNCTION__]);

        $data = array(
            'object_id' => $object_id,
            'object' => $object,
            'status_list' => $this->Object_Model->get_status_list(),
            // тип здания
            'type_of_building' => $this->Registry_Model->search(array('status' => \Registry_Model::STATUS_ACTIVE, 'handbk_id' => \Handbks_Model::TYPE_OF_BUILDING)),
            // серия здания
            'building_lot' => $this->Registry_Model->search(array('status' => \Registry_Model::STATUS_ACTIVE, 'handbk_id' => \Handbks_Model::BUILDING_LOT)),
            'registry_handbks' => $this->Registry_Model->prepare_registry_handbks($registry),
            'rooms' => $this->db->get('rooms')->result_array(),
        );



        $content = array(
            'action' => __FUNCTION__,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/technical_characteristics', $data, TRUE),
            'sections' => simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => (int) $object_id)), 'alias'),
            'current_section' => __FUNCTION__,
        );

        $this
                ->set_scripts(array(
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    public function cost($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('main_object.id', $object_id)->join('meta', 'meta.id_object = main_object.id', 'left')->get('main_object')->row_array()))
            show_404();

        if (($post = $this->input->post(NULL, TRUE))) {

            $this->db->where('id_object', $object_id)->update('meta', ['cost_anons' => array_get($post, 'cost_anons')]);

            $decorated_fields = array("space_min", "space_max", "cost_m_min", "cost_m_max", "cost_min", "cost_max",);

            // save flats
            if (($flats = array_get($post, 'flat'))) {
                foreach ($flats as $flat) {
                    $flat['object_id'] = $object_id;

                    foreach ($decorated_fields as $_field) {
                        $flat[$_field] = str_replace(' ', '', $flat[$_field]);
                    }

                    $this->Flats->update_room_by_object_id_room_id($flat);
                }
            }

            // save registry
            if (($registry_ids = $this->input->post('registry_id')) && is_array($registry_ids)) {
                $r = [];
                foreach ($registry_ids as $registry_id) {
                    if (!!($registry_id = (int) $registry_id) && !in_array($registry_id, $r))
                        $r[] = (int) $registry_id;
                }
                // clear old registry_ids
                $this->Registry_Model->delete_object_relations_by_handbks_groups_alias(__FUNCTION__, $object_id);
                // set new registry ids
                foreach ($r as $rid)
                    $this->db->insert('registry_has_main_object', array('object_id' => $object_id, 'registry_id' => $rid));
            }

            $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);
            redirect('/admin/objects/cost/' . $object_id);
        }

        $this->title = 'Цена';
        $this->set_breadcrumb($object['name'], '/admin/objects/general_info/' . $object_id);
        $this->set_breadcrumb($this->title);
        $this->title = $object['name'];

        // all registry handbks for current action
        $registry = $this->Registry_Model->search(['status' => \Registry_Model::STATUS_ACTIVE, 'with' => ['handbk_name'], 'handbks_groups_alias' => __FUNCTION__]);

        // current registry ids
        $registry_ids = $this->db->where('object_id', $object_id)->get('registry_has_main_object')->result_array();
        if ($registry_ids)
            $object['registry_id'] = array_keys(simple_tree($registry_ids, 'registry_id'));

        $data = array(
            'object_id' => $object_id,
            'object' => $object,
            'status_list' => $this->Object_Model->get_status_list(),
            'flats' => $this->Flats->get_flats_by_object_id($object_id),
            'registry_handbks' => $this->Registry_Model->prepare_registry_handbks($registry),
        );

        $content = array(
            'action' => __FUNCTION__,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/cost', $data, TRUE),
            'sections' => simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => (int) $object_id)), 'alias'),
            'current_section' => __FUNCTION__,
        );

        $this
                ->set_scripts(array(
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    /**
     * Post action
     * Album create
     * @param int $object_id
     */
    public function post_album_create($object_id = 0) {
        if (!$this->input->post('album_create'))
            redirect('/admin/');

        $this->load->model('Image_Albums');

        // validation form
        $this->form_validation->set_rules('album_name', 'Название', 'required');
        $this->form_validation->set_rules('object_id', 'Номер объекта', 'required');
        if (!$this->form_validation->run()) {
            $errors = $this->form_validation->get_errors();
            $this->session->set_userdata(__FUNCTION__ . '_' . (int) $object_id, array('form' => $this->input->post(), 'errors' => $errors));
        } else {
            $this->session->unset_userdata(__FUNCTION__ . '_' . (int) $object_id);
            // save data  
            $save = $this->Image_Albums->insert(array(
                'name' => $this->input->post('album_name'),
                'object_id' => (int) $this->input->post('object_id'),
                'file_category_id' => (int) $this->input->post('file_category_id'),
                'description' => $this->input->post('description'),
            ));
        }

        $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);
        // if isset route in form - redirect by route
        if ($this->input->post('route'))
            redirect($this->input->post('route'));
        // if $category_files - redirect by uri_adm 
        if (($category_files = (int) $this->input->post('file_category_id'))) {
            $this->load->model('File_Categories');
            $category_files = $this->File_Categories->get_by_primary_key($category_files);
            redirect(str_replace('{id}', (int) $object_id, element('uri_adm', $category_files, '/admin/')));
        }
        // undefined route
        redirect('/admin/');
    }

    /**
     * Post action
     * Album update
     * @param int $object_id
     * @param int $file_category_id
     */
    public function post_album_update($object_id = 0, $file_category_id = 0) {

        if (!$this->input->post('album_update'))
            redirect('/admin/');

        $file_category = $this->File_Categories->get_by_primary_key($file_category_id);
        if (empty($file_category))
            redirect('/admin/');

        $this->load->model('Image_Albums');

        $this->form_validation->set_rules('image_album_id', 'Номер объекта', 'required');
        if (!$this->form_validation->run()) {
            $errors = $this->form_validation->get_errors();
            $this->session->set_userdata(__FUNCTION__ . '_' . (int) $object_id, array('form' => $this->input->post(), 'errors' => $errors));
        } else {
            $this->session->unset_userdata(__FUNCTION__ . '_' . (int) $object_id);
            $post = $this->input->post();

            $album_images = $this->input->post('files') ? $this->input->post('files') : array();
            $sorts = $this->input->post('sort') ? $this->input->post('sort') : array();

            $files = array();
            foreach ($album_images as $file_id) {
                // prepare file data & set involves
                $files[$file_id] = array(
                    'file_id' => (int) $file_id,
                    'sort' => element($file_id, $sorts, $this->Image_Albums->get_defaul_sort_index()),
                    'file_involve_id' => $this->Image_Albums->insert(array(
                        'file_category_id' => (int) $file_category_id,
                        'file_id' => (int) $file_id,
                        'parent_id' => (int) $object_id,
                        'parent_alias' => element('alias', $object, ''),
                            ), 'file_involves'),
                );
            }

            // set files involves & save album data  
            $this->Image_Albums->update_album_images((int) $this->input->post('image_album_id'), $files);
            if ($this->input->post('image_album_id')) {
                $this->Image_Albums->update_by_primary_key((int) $post['image_album_id'], array(
                    'name' => element('name', $post, ''),
                    'description' => element('description', $post, ''),
                ));
            }
        }

        $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);

        // if isset route in form - redirect by route
        if ($this->input->post('route'))
            redirect($this->input->post('route'));
        // if file_category_id - redirect by uri_adm 
        if (!!$file_category_id) {
            $this->load->model('File_Categories');
            $category_files = $this->File_Categories->get_by_primary_key($file_category_id);
            redirect(str_replace('{id}', (int) $object_id, element('uri_adm', $category_files, '/admin/objects/general_info')));
        }

        // undefined route
        redirect('/admin/');
    }

    /**
     * Prepare for albums
     * @param int $object_id - id for object
     * @param int $file_category_id - id for file category 
     * @param bool $is_auto_render - [optional] default true
     * @return string - view
     */
    private function _albums($object_id = 0, $file_category_id = 0, $is_auto_render = TRUE) {
        // get form errors from album create
        $old = $this->session->userdata('post_album_create_' . $object_id);
        if (!$old) {
            $form = $errors = array();
        } else {
            $form = element('form', $old, array());
            $errors = element('errors', $old, array());
            $this->session->unset_userdata('post_album_create_' . $object_id);
        }

        // current object
        $object = $this->db->select('*, id as object_id')->where('id', (int) $object_id)->get('main_object')->row_array();
        if (empty($object))
            redirect('/admin/objects/general_info');

        $this->load->model('Image_Albums');

        // create album :: self::post_album_create()
        // update album :: self::post_album_update()

        $form['object_id'] = $object_id;
        $form['file_category_id'] = $file_category_id;

        // get albums
        $albums = $this->Image_Albums->get_by_object_id((int) $object_id, $file_category_id);

//        vdump($albums);

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

        if (isset($object['name']) && !$this->title)
            $this->title = $object['name'];

        $this->set_html_tpls($this->load->view($this->template_dir . 'html_tpls/card__item_view__tile', array(), TRUE));


        $content = $this->load->view($this->template_dir . 'objects/albums', array(
            'form' => $this->load->view($this->template_dir . 'forms/album', array('form' => $form, 'errors' => ($errors = json_encode($errors))), TRUE),
            'albums' => $albums,
            'is_show_tag' => !$is_auto_render,
                ), TRUE);

        $this->set_scripts_bottom('/js/object_cart.js');


        $this->after_body .= $this->load->view($this->template_dir . 'errors_form', array('errors' => $errors), TRUE);
        if ($is_auto_render)
            $this->content = $content;
        else
            return $content;
    }

    /**
     * Планировки
     * @param int $object_id
     */
    public function plan($object_id = 0) {
        $this->title = 'Планировки';
        $file_category = $this->File_Categories->get_by_field('prefix', 'plans');
        $file_category_id = array_get($file_category, 'file_category_id');
        // add widget files
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));

        $this->_base_section(__FUNCTION__, $object_id, array(
            'data' => array(
                'albums' => $this->_albums($object_id, $file_category_id, FALSE),
                'widget_storage' => $this->widget_storage->render(),
            )
        ));
    }

    /**
     * Фото строительства
     * @todo get $file_category_id
     * @param int $object_id
     */
    public function gallery($object_id = "") {
        $file_category = $this->File_Categories->get_by_field('prefix', 'photo_construction');
        $file_category_id = element('file_category_id', $file_category);
        $this->_albums($object_id, $file_category_id);
        $this->render();
    }

    public function documents($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        $this->title = 'Документы';
        $file_category = $this->File_Categories->get_by_field('prefix', 'docs');
        $file_category_id = array_get($file_category, 'file_category_id');

        $object = $this->db->where('id', $object_id)->get('main_object')->row_array();

        if (($post = $this->input->post())) {

            $files = array_get($post, 'files', []);
            $links = array_get($post, 'links', []);

            foreach ($links as $key => $it)
                if (!array_get($it, 'name') || !array_get($it, 'link'))
                    unset($links[$key]);

            $this->Storage_Files->set_files_involves($file_category_id, $object_id, array_get($object, 'alias', ''), array_unique($files));
            $this->Links->replace_object_links($object_id, $links);

            $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);
        }


        $files = $this->Storage_Files->get_by_category($file_category_id, $object_id);

        $links = $this->Links->get_by_field('object_id', $object_id, FALSE);


        // add widget files
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['docs', 'upload']));

        $this->after_body .= $this->load->view($this->template_dir . 'html_tpls/form_links', array(), TRUE);

        $this
                ->set_scripts_bottom('/js/object_cart.js')
                ->_base_section(__FUNCTION__, $object_id, array(
                    'data' => array(
                        'albums' => $this->_albums($object_id, $file_category_id, FALSE),
                        'widget_storage' => $this->widget_storage->render(),
                        'files' => $files,
                        'links' => $links,
                    )
                ));
    }

    public function infrastructure($object_id = "") {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            redirect('/admin/objects/general_info/');



        // load model Registry_Model
        $this->load->model('Registry_Model');

        if (($post = $this->input->post())) {

//            vdump($post);
            // update meta
            $this->db->where('id_object', $object_id)->update('meta', array('infrastructure' => array_get($post, 'text')));

            // clear old registry_ids
            $this->Registry_Model->delete_object_relations_by_handbks_groups_alias(__FUNCTION__, $object_id);

            // save registry
            if (($registry = array_get($post, 'registry')) && is_array($registry)) {
                foreach ($registry as $group_id => $group) {
                    foreach ($group as $registry_id => $item) {
                        $this->db->insert('registry_has_main_object', array(
                            'object_id' => $object_id,
                            'registry_id' => (int) $registry_id,
                            'group_id' => (int) $group_id,
                            'description' => array_get($item, 'description')
                        ));
                    }
                }
            }

            $this->add_status($object_id, 9);
            redirect('/admin/objects/infrastructure/' . $object_id);
        }

        $this->title = 'Инфраструктура';
        $this->set_breadcrumb($object['name'], '/admin/objects/general_info/' . $object_id);
        $this->set_breadcrumb($this->title);
        $this->title = $object['name'];

        $infrastructure_list = simple_tree_group($this->Handbks_Model->get_infrastructure_list(), 'category_name');
        // current registry ids
        $object_registry = $this->db->where('object_id', $object_id)->get('registry_has_main_object')->result_array();

        // infrastructure
        $file_category = $this->File_Categories->get_by_field('prefix', 'infrastructure');

        // add widget files
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));

        $data = array(
            'text' => array_get($object, 'infrastructure', ''),
            'object_id' => $object_id,
            'object' => $object,
            'status_list' => $this->Object_Model->get_status_list(),
            'infrastructure_list' => $infrastructure_list,
            'object_registry' => $object_registry,
            'albums' => $this->_albums($object_id, $file_category['file_category_id'], FALSE),
            'widget_storage' => $this->widget_storage->render(),
        );

        $content = array(
            'action' => __FUNCTION__,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/infrastructure', $data, TRUE),
            'sections' => simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => $object_id)), 'alias'),
            'current_section' => 'infrastructure',
        );

        $this
                ->set_scripts(array(
                    '/js/jquery_ext.js',
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    public function video($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        if (($post = $this->input->post())) {

            $this->db->where('id_object', $object_id)->update('meta', ['video' => array_get($post, 'video', '')]);
            $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);
            redirect('/admin/objects/video/' . $object_id);
        }

        $this->title = 'Видео';

        $this
                ->set_scripts(array(
                    '/js/jquery_ext.js',
                    '/vendor/ckeditor/ckeditor.js',
                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->_base_section(__FUNCTION__, $object_id, ['object' => $object, 'data' => ['object' => $object,]]);
    }

    /**
     * @deprecated ????????????
     * @param type $object_id
     */
    public function add_seller($object_id) {
        $image = '';
        if (isset($_FILES["logo"]["name"]) && (!empty($_FILES["logo"]["name"]))) {

            $filename = md5(date("d F Y H:i:s")) . $this->translitIt($_FILES["logo"]["name"]);
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/images/sellers/')) {
                mkdir($_SERVER['DOCUMENT_ROOT'] . '/images/sellers/');
            }
            $newpath = $_SERVER['DOCUMENT_ROOT'] . '/images/sellers/';
            move_uploaded_file($_FILES["logo"]["tmp_name"], $newpath . $filename);
            $url = '/images/sellers/' . $filename;
            $image = $url;
        }
        $this->db->insert('sellers_object', array(
            'company_name' => $this->input->post('company_name'),
            'adres' => $this->input->post('adres'),
            'sait' => $this->input->post('sait'),
            'phone1' => $this->input->post('phone1'),
            'phone2' => $this->input->post('phone2'),
            'info' => $this->input->post('info'),
            'logo' => $image,
        ));
//        redirect('/admin/objects/sellers/'.$object_id);
    }

    public function seo($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('main_object.id', $object_id)->join('meta', 'meta.id_object = main_object.id', 'left')->get('main_object')->row_array()))
            show_404();

        $seo = $this->db->where('parent_id', $object_id)->where('type', 'object')->get('meta_seo')->row_array();

        if (($post = $this->input->post())) {

            $upd = [
                'title' => array_get($post, 'title'),
                'keywords' => array_get($post, 'keywords'),
                'description' => array_get($post, 'description')
            ];

            if ($seo) {
                $this->db->where('parent_id', $object_id)->where('type', 'object')->update('meta_seo', $upd);
            } else {
                $this->db->insert('meta_seo', array_merge($upd, ['parent_id' => $object_id, 'type' => 'object',]));
            }

            redirect('/admin/objects/seo/' . $object_id);
        }

        $this->_base_section(__FUNCTION__, $object_id, [
            'object' => $object,
            'data' => $seo,
        ]);
    }

    public function publish($object_id = "") {
        $data = array();
        $meta = $this->db->where('id_object', $object_id)->get('meta')->row_array();
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $meta = str_repeat('0', 12);

            if ($this->input->post('category_switch'))
                foreach ($this->input->post('category_switch') as $key => $value)
                    $meta[$key] = '1';
            $this->db->where('id_object', $object_id)->update('meta', array('publish' => $meta));

            if ($this->input->post('is_compited'))
                $this->compite_add_object($object_id);

//            redirect('/admin/objects/publish/'.$object_id);
        } else {
            $data = array(
                'meta' => $meta,
                'object_id' => $object_id,
                'categories' => array('Описание', 'Местонахождение', 'Тех. характеристики', 'Стоимость', 'Планировки', 'Фото строительства',
                    'Видео', 'Документация', 'Инфраструктура', 'Застройщики', 'Продавцы', 'Карточка',)
            );
            $this->load->view('admin/v_publish', $data);
        }
    }

    public function delete_object() {
        $id = $this->input->post('id');

        $meta = $this->db->where('id_object', $id)->get('meta')->row_array();

        /** Местоположение (object_location) */
        @$this->db->where('object_id', $id)->delete('meta_metro');

        /** Файлы */
        $files = $this->db->get('files')->result();
        foreach ($images as $val) {
            if ($value->object_id == $id) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $val->file);
                $this->db->where('id', $val->id)->delete('files');
            }
        }
        $this->db->where('object_id', $id)->delete('files');
        /** Видео */
        $this->db->where('id_object', $id)->delete('video');
        /** Продавцы */
        $this->db->where('object_id', $id)->delete('meta_sellers');
        /** Застройщики */
        $this->db->where('object_id', $id)->delete('meta_builders');
        //$this->load->view('v_objectcard',$data);
        @$this->db->where('id', $id)->delete('main_object');
        @$this->db->where('id_object', $id)->delete('meta');
        @$this->db->where('id_object', $id)->delete('user_note');
        return true;
    }

    public function delete_files_images() {
        $ids = $this->input->post('ids');
        for ($i = 0; $i < count($ids); $i++) {
            $link = $this->db->select('file')->where('id', $ids[$i])->get('files')->row()->img;
            $this->db->where('id', $ids[$i]);
            $this->db->delete('files');
            unlink($_SERVER['DOCUMENT_ROOT'] . $link);
        }
    }

    function add_status($id, $num) {

        if ($meta = $this->db->where('id_object', $id)->get('meta')->row_array()) {
            $meta = $meta['fullness'] ? $meta['fullness'] : str_repeat('0', 12);
            if ($num <= strlen($meta))
                $meta[$num - 3] = '1';
            $this->db->where('id_object', $id)->update('meta', array('fullness' => $meta));
        }

        $this->db->where('id', $id)->update('main_object', array('add_status' => $num, 'updated' => date('Y-m-d H:i:s')));
    }

    function compite_add_object($object_id) {
        $this->db->where('id', $object_id)->update('main_object', array('status' => 1, 'updated' => date('Y-m-d H:i:s')));
        $this->db->where('id_object', $object_id)->update('meta', array('add_status' => 1));
        redirect('admin/objects');
    }

    function rename_album() {
        echo $id = $this->input->post('id');
        echo $album_name = $this->input->post('album_name');
        echo $old_name = $this->input->post('old_name');
        echo $table = $this->input->post('table');
        $this->db->where('id', $id)->update($table, array('name' => $album_name));
    }

    private function translitIt($str) {
        return transliteration($str);
//        $tr = array(
//            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
//            "Е" => "E", "Ё" => "YO", "Ж" => "J", "З" => "Z", "И" => "I",
//            "Й" => "J", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
//            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
//            "У" => "U", "Ф" => "F", "Х" => "KH", "Ц" => "C", "Ч" => "CH",
//            "Ш" => "SH", "Щ" => "SHCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
//            "Э" => "EH", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
//            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo", "ж" => "j",
//            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
//            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
//            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh",
//            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shch", "ъ" => "y",
//            "ы" => "yi", "ь" => "", "э" => "eh", "ю" => "yu", "я" => "ya",
//            " " => "-", "-" => "-", "—" => "-", "(" => "", ")" => "", "«" => "",
//            "»" => "", "," => "", "%" => "", "." => "", "/" => "", "\'" => "",
//            "*" => "", "?" => "", "&" => "", "^" => "", ":" => "", ";" => "", "#" => "",
//            "<" => "", ">" => ""
//        );
//        return strtr($str, $tr);
    }

    public function update_comments() {
        $comments = $this->input->post('comment');
        $table_name = $this->input->post('status');
        if ($table_name == 'files') {
            $field = 'name';
        } else {
            $field = 'comment';
        }
        for ($i = 0; $i < count($comments) / 2; $i++) {
            $this->db->where('id', $comments['id' . $i])->update($table_name, array($field => $comments['comment' . $i]));
        }
    }

    public function find_icon_metro() {
        $metro_name = $this->input->post('metro');
        $result = @$this->db->where('name', $metro_name)->select('image_type')->get('metro')->row()->image_type;
        echo $result;
    }

    /**
     * Get navs fo current Controller
     * @param array/int $object
     * @return string
     */
    private function get_nav($object = array()) {
        if (is_numeric($object))
            $object = $object = $this->db->where('id', $object)->get('main_object')->row_array();

        $nav = array(
            array('name' => 'Описание', 'path' => '/admin/objects/general_info/'),
            array('name' => 'Местонахождение', 'path' => '/admin/objects/object_location/'),
            array('name' => 'Tex. характеристики', 'path' => '/admin/objects/technical_characteristics/'),
            array('name' => 'Стоимость', 'path' => '/admin/objects/cost/'),
            array('name' => 'Планировки', 'path' => '/admin/objects/plan/'),
            array('name' => 'Фото строительства', 'path' => '/admin/objects/gallery/'),
            array('name' => 'Видео', 'path' => '/admin/objects/video/'),
            array('name' => 'Документация', 'path' => '/admin/objects/documents/'),
            array('name' => 'Инфраструктура', 'path' => '/admin/objects/infrastructure/'),
            array('name' => 'Застройщики', 'path' => '/admin/objects/builders/'),
            array('name' => 'Продавцы', 'path' => '/admin/objects/sellers/'),
            array('name' => 'Карточка', 'path' => '/admin/cart/'),
            array('name' => 'Мета-теги', 'path' => '/admin/objects/seo/'),
            array('name' => 'Панорамы', 'path' => '/admin/objects/panorama/'),
            array('name' => 'План застройки', 'path' => '/admin/objects/layout_plan/'),
            array('name' => 'Публикация', 'path' => '/admin/objects/publish/'),
        );

        return $this->load->view($this->template_dir . 'navs/tabs_object', array('list' => $nav, 'object' => $object), TRUE);
    }

    public function layout_plan($object_id) {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        if (($post = $this->input->post())) {

            $this->db->where('id_object', $object_id)->update('meta', array(
                'layout_plan' => array_get($post, 'layout_plan'),
                'layout_plan_map' => array_get($post, 'layout_plan_map'),
            ));

            redirect('/admin/objects/layout_plan/' . $object_id);
        }


        $this->title = 'План застройки';

        $this
                ->set_scripts(array(
                    '/js/jquery_ext.js',
                    '/vendor/ckeditor/ckeditor.js',
//                    '/vendor/ckeditor/drop_cache.js',
                    '/vendor/ckeditor/config.js',
                    '/vendor/ckeditor/styles.js',
                ))
                ->set_scripts_bottom(['/js/dashboard/objectcard.js', '/js/dashboard/object_location.js',]);


        $file_category = $this->File_Categories->get_by_field('prefix', __FUNCTION__);
        $file_category_id = array_get($file_category, 'file_category_id');
        // add widget files
        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'upload']));

        $this->_base_section(__FUNCTION__, $object_id, array(
            'object' => $object,
            'data' => array(
                'albums' => $this->_albums($object_id, $file_category_id, FALSE),
                'widget_storage' => $this->widget_storage->render(),
            )
        ));
    }

    /**
     * Базовая структура для секции настроек объекта
     * @param string $action - __FUNCTION__
     * @param type $object_id - номер объекта
     * @param array $settings - настройки:<br>
     * <b>data</b> - array - данные для текущей view<br>
     * <b>object</b> - array - текущий объект<br>
     */
    private function _base_section($action, $object_id, array $settings = []) {

        // проверяем корректность объекта
        if (($object = array_get($settings, 'object'))) {
            if (!is_array($object) || (int) $object_id !== (int) array_get($object, 'id_object'))
                show_404();
        } else {
            if (!($object_id = (int) $object_id) || !($object = $this->db->where('main_object.id', $object_id)->join('meta', 'meta.id_object = main_object.id', 'left')->get('main_object')->row_array()))
                show_404();
        }



        $this->set_breadcrumb($object['name'], '/admin/objects/general_info/' . $object_id);
        $this->set_breadcrumb($this->title);
        $this->title = $object['name'];

        $data = array_merge(array(
            'object_id' => $object_id,
            'object' => $object,
            'status_list' => $this->Object_Model->get_status_list(),
        ), array_get($settings, 'data', array()));

        $content = array(
            'action' => $action,
            'object' => $object,
            'object_id' => $object_id,
            'breadcrumbs' => $this->get_breadcrumb(),
            'title' => $this->title,
            'content' => $this->load->view($this->template_dir . 'objects/' . $action, $data, TRUE),
            'sections' => simple_tree($this->Object_Section_Model->search(array('order' => 'sort,name', 'is_has_object' => $object_id)), 'alias'),
            'current_section' => $action,
        );

        $this
                ->set_scripts_bottom(['/js/dashboard/objectcard.js',])
                ->render($this->load->view($this->template_dir . 'pages/objectcard', $content, TRUE));
    }

    public function builders_sellers($object_id) {
        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        $this->load->model('Organizations_Model');

        $organization_types = $this->Organizations_Model->get_types();

        if (($post = $this->input->post())) {

            $organization = array_get($post, 'organizations', array());

            foreach ($organization_types as $type) {
                $this->Object_Model->set_organizations($object_id, array_get($organization, $type['organization_type_id'], []), $type['organization_type_id']);
            }
            // upd object
            $this->db->where('id', $object_id)->update('main_object', array('updated' => date('Y-m-d H:i:s')));

            redirect('/admin/objects/builders_sellers/' . $object_id);
        }


        $this->title = 'Застройщик и продавцы';


        $organizations = array();

        foreach ($organization_types as $type) {

            $organizations[$type['organization_type_id']] = $type;
            $organizations[$type['organization_type_id']]['list'] = $this->Organizations_Model->search(array('organization_type_id' => $type['organization_type_id']));
            $organizations[$type['organization_type_id']]['current'] = $this->Object_Model->get_organizations($object_id, $type['organization_type_id']);
        }

        $this
                ->set_scripts_bottom(array('/js/dashboard/objectcard.js',))
                ->_base_section(__FUNCTION__, $object_id, array(
                    'object' => $object,
                    'data' => array(
                        'organizations' => $organizations,
                    ),
                ));
    }

    public function card($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        $this->title = "Карточка";

        $file_category = $this->File_Categories->get_by_field('prefix', 'card');
        $file_category_id = (int) array_get($file_category, 'file_category_id');

        if (($post = $this->input->post())) {

//            vdump($post);
            // set new images
            $files = $sorts = array();
            for ($i = 1; $i < 4; $i++) {
                if (( $file_id = element('file_' . $i, $post, 0))) {
                    $files[] = $file_id;
                    $sorts[$file_id] = $i;
                    // update alt 
                    $this->Storage_Files->update_by_primary_key($file_id, array('alt' => element('text_' . $i, $post, 0)));
                }
            }
            if (!empty($files))
                $this->Storage_Files->set_files_involves($file_category_id, $object_id, element('alias', $object, ''), $files, $sorts);

            $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);

            redirect('admin/objects/card/' . $object_id);
        }


        $this->load->library('Widget_storage', array('this' => $this, 'category' => $file_category_id, 'sections' => ['images', 'filters']));

        // get images
        $images = simple_tree($this->Storage_Files->get_by_category($file_category_id, $object_id), 'sort');

//        vdump($images);

        $schema = array(1 => '140x215', 2 => '286x215', 3 => '432x215');
        $data = [];

        foreach ($schema as $key => $size) {
            $data['images_simple_upload'][] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
                'image' => element($key, $images, array()),
                'attr' => array('image_class' => 'cart_' . $size, 'class' => 'cart_image_uploader'),
                'title' => 'Формат (' . $size . ')px',
                'input_name' => 'file_' . $key,
                'upload_place_content' => $this->load->view($this->template_dir . 'objects/cart_form_component', array('data' => element($key, $images, array()), 'index' => $key), TRUE),
                'filters' => json_encode(array()),
                    ), TRUE);
        }

        $this->set_scripts_bottom('/js/object_cart.js');

        $data['widget_storage'] = $this->widget_storage->render('popup', array('is_mass_edit' => FALSE, 'is_filter' => TRUE));

        return $this->_base_section(__FUNCTION__, $object_id, array(
                    'data' => $data,
        ));
    }

    public function mortgage($object_id = 0) {

        if (!$object_id || !($object = $this->db->where('id_object', (int) $object_id)->join('main_object', 'main_object.id = id_object')->get('meta')->row_array()))
            show_404();

        return $this->_base_section(__FUNCTION__, $object_id, array(
                    'data' => array(),
        ));
    }

}
