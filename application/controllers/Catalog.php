<?php
class Catalog extends MY_FrontController {

    /**
     * Model
     * @var \Search_Model
     */
    public $Search_Model;

    /**
     * Model
     * @var \Geo
     */
    public $Geo;

    /**
     * Model
     * @var \District_Model
     */
    public $District_Model;

    /**
     * Model
     * @var \Square_Model
     */
    public $Square_Model;

    /**
     * Model
     * @var \Geo_Area_Model
     */
    public $Geo_Area_Model;

    /**
     * Model
     * @var \Populated_Locality_Model
     */
    public $Populated_Locality_Model;

    /**
     * Model
     * @var \Metro_Station_Model
     */
    public $Metro_Station_Model;

    /**
     * Model
     * @var \Object_Model
     */
    public $Object_Model;

    /**
     * Model
     * @var \File_Categories
     */
    public $File_Categories;

    /**
     * Model
     * @var \Posts_Model
     */
    public $Posts_Model;

    /**
     * Widget
     * @var \Widget_gallery 
     */
    public $widget_gallery;

    /**
     * no init
     * @var \Registry_Model 
     */
    public $Registry_Model;

    /**
     *
     * @var array 
     */
    private $default_order = array(
        'order' => 'cost',
        'order_direction' => 'asc'
    );

    public function __construct() {
        parent::__construct();
        session_start();

        $this->load->model('Search_Model');
        $this->load->model('Geo');
        $this->load->model('District_Model');
        $this->load->model('Square_Model');
        $this->load->model('Geo_Area_Model');
        $this->load->model('Populated_Locality_Model');
        $this->load->model('Metro_Station_Model');
        $this->load->model('Object_Model');
        $this->load->model('File_Categories');
        $this->load->model('Posts_Model');

        $this->load->library('session');
    }

    /**
     * main page
     * @todo rewrite this shit
     */
    public function index() {

//        vdump($this->Object_Model->get_by_zone('MOS'));
//        $a = b();
        $data = array();
        $this->title .= ' - Новостройки Москвы и Московской области';
        $this->meta_description = 'Каталог новостроек Москвы и Московской области. Цены на квартиры от застройщиков, планировки квартир, фотографии строительства. Новости рынка недвижимости.';
        $get = xss_clean($_GET);
        $base_url = '/catalog';

        // pagination      
        $this->load->library('flpagination');

        // type view
        $view_type = !!($vt = array_get($get, 'vt')) ? $vt : (!!($vt = $this->session->userdata('view_type')) ? $vt : 'tiles');



        $data['objects'] = $this->Object_Model->get_short_list_by_ids(
                FALSE, $view_type === 'map' ? false : $this->flpagination->get_limit(), $this->flpagination->get_offset(), element('sf', $get, 'cost'), element('sd', $get, 'asc')
        );


        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $count = (int) element('FOUND_ROWS()', $count, 0);

        $message = $this->load->view($this->template_dir . 'pages/search_message', array('message' => 'Всего новостроек - ' . $count), TRUE);

        $data['pagination'] = $this->flpagination->pagination(array(
            'total_rows' => $count, // 100,
            'base_url' => $base_url,
        ));

        // check view type (vt)
        if ($view_type) {
            switch ($view_type) {
                case 'list':
                    $view_tpl = 'catalog_list';
                    break;
                case 'map':
                    $view_tpl = 'catalog_map';
                    $this->set_scripts('https://api-maps.yandex.ru/2.1/?lang=ru_RU' . (ENVIRONMENT !== 'production' ? '&mode=debug' : ''))
                            ->set_scripts_bottom('map.js')
                            ->set_styles('style_map.css')
                    ;

                    $features = array();
                    foreach ($data['objects'] as $item) {
                        $item = $this->Object_Model->prepare_object($item, array(
                            'truncate_list' => array('address' => 45, 'name' => 50),
                            'image' => 'image_1',
                            'cost' => array('cost_min'),
                            'delivery' => TRUE,
                        ));

                        $features[] = array(
                            'type' => 'Feature',
                            'id' => array_get($item, 'id'),
                            'geometry' => array('type' => 'Point', 'coordinates' => array(array_get($item, 'y'), array_get($item, 'x'))),
                            'properties' => $item,
                        );
                    }
                    $data['map'] = array('type' => 'FeatureCollection', 'features' => $features);

                    break;
                default :
                    $view_tpl = 'catalog_tiles';
            }

            $this->session->set_userdata('view_type', $view_type);
        }

        $view_objects = $this->load->view($this->template_dir . 'pages/' . $view_tpl, $data, TRUE);

        $this->body = $this->load->view($this->template_dir . 'pages/catalog', array(
            'message' => $message,
            'base_url' => $base_url,
            'is_show_controls' => !empty($data['objects']),
            'view_objects' => $view_objects,
            'view_type' => $view_type,
            'get' => $get,
                ), TRUE);

        $this->render();

    }

    /**
     * Search
     * @return boolean
     */
    public function search() {

        $data = array();
        $result = array();

//        var_dump('ssss');
        $this->title .= ' - Каталог новостроек Москвы и Московской области. Цены от застройщиков, планировки и фотографии квартир';
        $this->meta_description = 'Каталог новостроек Москвы и Московской области. Цены на квартиры от застройщиков, планировки квартир, фотографии строительства. Новости рынка недвижимости.';

        $get = xss_clean($_GET);

//        vdump($get, 1);

        if (empty($get))
            redirect('/catalog');

//        $result = $this->Search_Model->search_object_ids($get);

        $result = $this->Search_Model->search_ids($get);
//        vdump($this->db->last_query(), 1);

        $this->_render_result($result, $data);
    }

    /**
     * Render search results
     * @param array $result - object ids in keys
     * @param array $data - data for view:<br>
     * <b>title</b> - string - page title;<br>
     * <b>base_url</b> - string - page url;<br>
     * <b>objects</b> - array - list of objects;<br>
     */
    private function _render_result($object_ids, $data) {
        $this->title = array_get($data, 'title', 'Flatory.ru - Каталог новостроек по Москве и Московской области. Планировки квартир, стоимость и фотографии строительства');
        $get = xss_clean($this->input->get());
        $get = is_array($get) ? $get : [];
        $base_url = array_get($data, 'base_url', '/catalog/search');

        // type view
        $view_type = !!($vt = array_get($get, 'vt')) ? $vt : (!!($vt = $this->session->userdata('view_type')) ? $vt : 'tiles');

        // если что-то найдено
        if (count($object_ids) > 0 || (is_array(array_get($data, 'objects')) && count($data['objects']) > 0)) {

            // pagination      
            $this->load->library('flpagination');

            if (!array_get($data, 'objects'))
                $data['objects'] = $this->Object_Model->get_short_list_by_ids(
                        array_keys($object_ids), $view_type === 'map' ? false : $this->flpagination->get_limit(), $this->flpagination->get_offset(), element('sf', $get, 'cost'), element('sd', $get, 'asc')
                );

            $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
            $count = (int) element('FOUND_ROWS()', $count, 0);

            $message = $this->load->view($this->template_dir . 'pages/search_message', array('message' => 'Всего новостроек - ' . $count), TRUE);

            $data['pagination'] = $this->flpagination->pagination(array(
                'total_rows' => $count, // 100,
                'base_url' => $base_url,
            ));
        } else {
            $message = $this->load->view($this->template_dir . 'pages/search_message', ['message_no_found' => isset($this->message_no_found) ? $this->message_no_found : NULL], TRUE);
        }

        // check view type (vt)
        if ($view_type) {
            switch ($view_type) {
                case 'list':
                    $view_tpl = 'catalog_list';
                    break;
                case 'map':
                    $view_tpl = 'catalog_map';
                    $this->set_scripts('https://api-maps.yandex.ru/2.1/?lang=ru_RU' . (ENVIRONMENT !== 'production' ? '&mode=debug' : ''))
                            ->set_scripts_bottom('map.js')
                            ->set_styles('style_map.css')
                    ;

                    $features = [];
                    foreach ($data['objects'] as $item) {
                        $item = $this->Object_Model->prepare_object($item, array(
                            'truncate_list' => array('address' => 45, 'name' => 50),
                            'image' => 'image_1',
                            'cost' => array('cost_min'),
                            'delivery' => TRUE,
                        ));

                        $features[] = array(
                            'type' => 'Feature',
                            'id' => array_get($item, 'id'),
                            'geometry' => array('type' => 'Point', 'coordinates' => array(array_get($item, 'y'), array_get($item, 'x'))),
                            'properties' => $item,
                        );
                    }
                    $data['map'] = array('type' => 'FeatureCollection', 'features' => $features);

                    break;
                default :
                    $view_tpl = 'catalog_tiles';
            }

            $this->session->set_userdata('view_type', $view_type);
        }

        $view_objects = $this->load->view($this->template_dir . 'pages/' . $view_tpl, $data, TRUE);

        $this->body = !$this->body ? '' : $this->body;

        $this->body .= $this->load->view($this->template_dir . 'pages/catalog', array(
            'message' => $message,
            'base_url' => $base_url,
            'is_show_controls' => !empty($data['objects']),
            'view_objects' => $view_objects,
            'view_type' => $view_type,
            'get' => $get,
            'default_order' => $this->default_order,
                ), TRUE);
        $this->render();
    }

    /**
     * Карточка объекта
     * @param string $alias - алиас обьекта
     */
    public function objectcard($alias = "") {
        // load gallery widget
        $this->load->library('Widget_gallery', array('this' => $this));

        $object = $this->Object_Model->get_by_alias($alias);

        if (!($object_id = $object->id()))
            show_404();


//
        $current = $object->object();

        // декорируем цены
//        $current['cost']['cost_min'] = number_format(element('cost_min', $current['cost'], 0), 0, ',', ' ');
        $current['cost']['cost_max'] = $this->_int_to_str(element('cost_max', $current['cost'], 0));
        // фото строительства
        $current['photo_construction'] = $object->get_albums();
        if (array_get($current, 'infrastructure'))
            $current['infrastructure'] = $this->widget_gallery->replace_gallery_marks($current['infrastructure']);
        if (array_get($current, 'layout_plan'))
            $current['layout_plan'] = $this->widget_gallery->replace_gallery_marks($current['layout_plan']);

        // определяем нужно ли выводить раздел цены
        $flats = array_get($current, 'flats');
        $price_flats = [];
        if (is_array($flats)) {
            foreach ($flats as $key => $item) {
                if ((int) array_get($item, 'cost_m_min') || (int) array_get($item, 'cost_m_max') || (int) array_get($item, 'cost_min') || (int) array_get($item, 'cost_max'))
                    $price_flats[] = $item;
            }
        }
        $current['price_flats'] = $price_flats;

        // load model Registry_Model
        $this->load->model('Registry_Model');
        // получаем параметры из общих справочников
        $registry = $this->Registry_Model->search(array('object_id' => $object_id, 'status' => \Registry_Model::STATUS_ACTIVE, 'with' => array('handbk_name')));

        $current['posts'] = $this->Object_Model->get_posts($object_id, array('limit' => 10, 'offset' => 0));

        $infrastructure = $this->Object_Model->infrastructure($object_id, array('with' => array('parse_params')));

        $inf = array();
        foreach ($infrastructure as $key => $item) {
            if (!isset($inf[$group_id = array_get($item, 'group_id')])) {
                $inf[$group_id] = array();
            }

            if (!isset($inf[$group_id][$category_id = array_get($item, 'category_id')])) {
                $inf[$group_id][$category_id] = array(
                    'category_id' => $category_id,
                    'category_name' => array_get($item, 'category_name'),
                    'params' => array_get($item, 'params'),
                    'items' => array(),
                );
            }

            $inf[$group_id][$category_id]['items'][] = !array_get($item, 'description') ? array_get($item, 'name') : $item['description'];
        }

//        vdump($current);
//        vdump($this->Registry_Model->prepare_registry_handbks($registry));

        $data = array(
            'object' => $current,
            // object images
            'gallery' => $this->widget_gallery->render($object->images()),
            // pluns
            'pluns' => $this->widget_gallery->render_albums($current['pluns']),
            // photo_construction
            'photo_construction' => $this->widget_gallery->render_albums($current['photo_construction']),
            // glosary link tpl
            'glossary_link_tpl' => element('uri', $this->File_Categories->get_by_field('prefix', 'glossary')),
            // registry_handbks
            'registry_handbks' => $this->Registry_Model->prepare_registry_handbks($registry),
            // panorama
            'panoram_types' => array(
                'panorama_ya' => 'Яндекс',
                'panorama_ggl' => 'Google',
            ),
            'infrastructure' => $inf,
            'infrastructure_groups' => ['Собственная инфраструктура', 'Инфраструктура района'],
        );

//        vdump($current['x'], 1);
//        vdump($current['y'], 1);

        $this->set_scripts_bottom(array('catalog.js', 'map_route.js'));

        // подключаем карту
        {
            $this
                    ->set_scripts('https://api-maps.yandex.ru/2.1/?lang=ru_RU' . (ENVIRONMENT !== 'production' ? '&mode=debug' : ''))
                    ->set_scripts_bottom('map_route.js');
            $features = array();
            $item = $this->Object_Model->prepare_object($current, array(
                'truncate_list' => array('address' => 45, 'name' => 50),
                'image' => 'image_1',
                'cost' => array('cost_min'),
                'delivery' => TRUE,
                'space' => TRUE,
            ));

            $features[] = array(
                'type' => 'Feature',
                'id' => array_get($item, 'id'),
                'geometry' => array('type' => 'Point', 'coordinates' => $map_center = array(array_get($item, 'y'), array_get($item, 'x'))),
                'properties' => $item,
            );
            $data['map'] = array('type' => 'FeatureCollection', 'features' => $features);
            $data['map_center'] = $map_center;
        }

        // подключаем рекламу
        {
            $data['adv1'] = $this->load->view('adv/vbutovo/440x90', array(), TRUE);
        }
        // получаем список секций объекта
        $data['sections'] = pluck_key_value($this->db->select('s.object_section_id, alias')
                        ->from('object_sections_has_main_object as rel')
                        ->join('object_sections as s', 's.object_section_id = rel.object_section_id', 'left')
                        ->where('object_id', $object_id)
                        ->get()->result_array(), 'object_section_id', 'alias');

//        vdump($data['sections']);

        $data['seo'] = $this->db->where('parent_id', $object->id())->where('type', 'object')->get('meta_seo')->row_array();
        $this->title = element('title', $data['seo'], element('name', $current, ''));
        $this->meta_description = element('description', $data['seo'], '');
        $this->meta_keywords = element('keywords', $data['seo'], '');

        $this->body = $this->load->view($this->template_dir . 'pages/objectcard', $data, TRUE);

        $this->render();
    }

    public function map() {

        $this->title = 'Новостройки Москвы и Московской области на карте';
        $this->meta_description = 'Поиск новостроек Москвы и Московской области на карте. Подбор по параметрам: площадь, количество комнат, стоимость, наличие отделки и срок ввода.';
        $this->header = '';
        $this->footer = '';

        $this->content = $this->load->view($this->template_dir . 'pages/search_map', array(), TRUE);
        $this->styles = array('style_map.css');
        $this->scripts_bottom = array('map.js');

        $this
                ->set_scripts('https://api-maps.yandex.ru/2.1/?lang=ru_RU' . (ENVIRONMENT !== 'production' ? '&mode=debug' : ''));

        $this->render();
    }

    /**
     * Some crutches
     * @todo rewrite this shit
     * @param type $num
     * @return type
     */
    private function _int_to_str($num) {
        $mln = '';
        $th = '';
        if (strlen(round(abs($num))) > 6) {
            $mln = substr($num, 0, strlen(round(abs($num))) - 6) . 'млн.';
            //return $new_string;
        }
        $ths = substr($num, strlen(round(abs($num))) - 6);
        $nums = substr($ths, 0, 3);
        if (strlen(round(abs($ths))) > 3 && (int) $nums > 0) {
            $th = (int) $nums . 'тыс.';
        }
        $nm = substr($num, strlen(round(abs($num))) - 3);
        $cl = ((int) $nm > 0) ? (int) $nm : '';
        return $mln . ' ' . $th . ' ' . $cl;
    }

    public function geo($region_alias, $param1 = FALSE, $param2 = FALSE) {
        $this->message_no_found = 'Здесь пока нет новостроек';
        if (method_exists($this, $method_name = '_geo_' . str_replace('-', '_', $region_alias))){
            $this->$method_name($param1, $param2);
        }else{
            show_404();
        }
    }

    /**
     * @todo refactoring!
     * @param type $district_alias
     * @param type $square_alias
     * @return type
     */
    private function _geo_novaya_moskva($district_alias = FALSE, $square_alias = FALSE) {

        $this->title = 'Новостройки Новой Москвы';
        $base_url = '/' . $this->uri->segment(1) . '/';
        $url = '/' . $this->uri->uri_string();

        $filters = array_merge(array('district_id' => $district_ids = array(10, 12),), $this->_get_order());

        if (!$district_alias) {
            // moskow page - show district list
            $districts = $this->District_Model->search(array('order' => 'name', 'with' => array('posts'), 'district_id' => $district_ids, 'status' => District_Model::STATUS_ACTIVE));

            // get cat & post 
            $cat = $this->File_Categories->get_by_field('prefix', 'macro_region');
//            if (!($category_id = (int) array_get($cat, 'file_category_id')))
//                show_404();
//            $post = $this->Posts_Model->search(array(
//                'file_category_id' => $category_id,
//                'alias' => $this->uri->segment(1)
//            ), TRUE);
//
            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
//                'post' => !!$post ? $post : array('name' => $this->title),
                'geo_index_list' => $districts,
                'geo_index_title' => $this->title . ' по округам',
            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
            return;
        }

        $this->breadcrumbs_add($this->title, $base_url);

        // district page
        $cat = $this->File_Categories->get_by_field('prefix', 'district');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $district_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['district_id'] = (int) array_get($post, 'object_id');

        if (!in_array($filters['district_id'], $district_ids) || !($district = $this->District_Model->search(['district_id' => $filters['district_id'], 'status' => District_Model::STATUS_ACTIVE], TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($district, 'name'), $base_url . $district_alias . '/');


        if (!$square_alias) {
            // moskow district page - show square list

            $square = $this->Square_Model->search(array(
                'order' => 'name',
                'post_parent_alias' => $district_alias,
                'with' => array('posts', 'post_parent_alias'),
                'status' => Square_Model::STATUS_ACTIVE
            ));

            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
                'post' => $post,
                'geo_index_list' => $square,
                'geo_index_title' => $this->title . ' по районам',
                'path_url' => $url . '/'
            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result([], array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
            return;
        }

        // moskow square page
        $cat = $this->File_Categories->get_by_field('prefix', 'square');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $square_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['square_id'] = (int) array_get($post, 'object_id');

        if (!($square = $this->Square_Model->search(array('square_id' => $filters['square_id'], 'status' => Square_Model::STATUS_ACTIVE), TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($square, 'name'), $url);


        $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', ['post' => $post], TRUE);
        // get objects
        $objects = $this->Object_Model->search($filters);
        $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
    }

    /**
     * @todo refactoring!
     * @param type $district_alias
     * @param type $square_alias
     * @return type
     */
    private function _geo_moskva($district_alias = FALSE, $square_alias = FALSE) {


//        var_dump('sssss');die;
//        vdump( District_Model::STATUS_ACTIVE);
        // $param1 = district_alias
        // $param2 = square_alias

        $this->title = 'Новостройки Москвы';
        $base_url = '/' . $this->uri->segment(1) . '/';
        $url = '/' . $this->uri->uri_string();


        $filters = array_merge(array('zone_id' => 2761,), $this->_get_order());
        if (!$district_alias) {

            // moskow page - show district list
            $districts = $this->District_Model->search(array('order' => 'name', 'with' => array('posts'), 'status' => District_Model::STATUS_ACTIVE));
            // get cat & post 
            $cat = $this->File_Categories->get_by_field('prefix', 'macro_region');

            $category_id = (int) array_get($cat, 'file_category_id');
//            echo'<pre>';
//            var_dump($cat);die;
//            if (!($category_id = (int) array_get($cat, 'file_category_id')))
//                show_404();
//            $post = $this->Posts_Model->search(array(
//                'file_category_id' => $category_id,
//                'alias' => $this->uri->segment(1)
//            ), TRUE);

            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
//                'post' => !!$post ? $post : array('name' => $this->title),
                'geo_index_list' => $districts,
                'geo_index_title' => $this->title . ' по округам',
            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result([], array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
            return;
        }

        $this->breadcrumbs_add($this->title, $base_url);

        // district page
        $cat = $this->File_Categories->get_by_field('prefix', 'district');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $district_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['district_id'] = (int) array_get($post, 'object_id');

        if (!($district = $this->District_Model->search(array('district_id' => $filters['district_id'], 'status' => District_Model::STATUS_ACTIVE), TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($district, 'name'), $base_url . $district_alias . '/');


        if (!$square_alias) {
            // moskow district page - show square list

            $square = $this->Square_Model->search(array('order' => 'name', 'post_parent_alias' => $district_alias, 'with' => array('posts', 'post_parent_alias'), 'status' => Square_Model::STATUS_ACTIVE));

            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
                'post' => $post,
                'geo_index_list' => $square,
                'geo_index_title' => $this->title . ' по районам',
                'path_url' => $url . '/'
            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result([], array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
            return;
        }

        // moskow square page
        $cat = $this->File_Categories->get_by_field('prefix', 'square');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $square_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['square_id'] = (int) array_get($post, 'object_id');

        if (!($square = $this->Square_Model->search(array('square_id' => $filters['square_id'], 'status' => Square_Model::STATUS_ACTIVE), TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($square, 'name'), $url);


        $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array('post' => $post), TRUE);
        // get objects
        $objects = $this->Object_Model->search($filters);
        $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
    }

    private function _geo_moskovskaya_oblast($geo_area_alias = FALSE, $populated_locality_alias = FALSE) {



        // $param1 = $geo_area_alias
        // $param2 = populated_locality_alias

        $this->title = 'Новостройки Московской области';
        $base_url = '/' . $this->uri->segment(1) . '/';
        $url = '/' . $this->uri->uri_string();

        $filters = array_merge(array('zone_id' => 2722,), $this->_get_order());


        if (!$geo_area_alias) {
            // mo page - show geo_area list
            $geo_area = $this->Geo_Area_Model->search(array('order' => 'name', 'with' => array('posts'), 'status' => Geo_Area_Model::STATUS_ACTIVE));
//            var_dump($geo_area);die;
            // get cat & post
            $cat = $this->File_Categories->get_by_field('prefix', 'macro_region');
//            if (!($category_id = (int) array_get($cat, 'file_category_id')))
//                show_404();
//            $post = $this->Posts_Model->search(array(
//                'file_category_id' => $category_id,
//                'alias' => $this->uri->segment(1)
//            ), TRUE);

//            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
//                'post' => !!$post ? $post : array('name' => $this->title),
//                'geo_index_list' => $geo_area,
//                'geo_index_title' => $this->title . ' по районам',
//            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $base_url,));
            return;
        }
        $this->breadcrumbs_add($this->title, $base_url);

        // geo_area page
        $cat = $this->File_Categories->get_by_field('prefix', 'geo_area');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $geo_area_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['geo_area_id'] = (int) array_get($post, 'object_id');

        if (!($geo_area = $this->Geo_Area_Model->search(array('geo_area_id' => $filters['geo_area_id'], 'status' => Geo_Area_Model::STATUS_ACTIVE), TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($geo_area, 'name'), $base_url . $geo_area_alias . '/');

        if (!$populated_locality_alias) {
            // mo geo_area page - show populated_locality list

            $populated_locality = $this->Populated_Locality_Model->search(array(
                'order' => 'name',
                'post_parent_alias' => $geo_area_alias,
                'with' => array('posts', 'post_parent_alias'),
                'status' => Populated_Locality_Model::STATUS_ACTIVE)
            );

            $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array(
                'post' => $post,
                'geo_index_list' => $populated_locality,
                'geo_index_title' => $this->title . ' по населенным пунктам',
                'path_url' => $url . '/'
            ), TRUE);

            $objects = $this->Object_Model->search($filters);

            $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
            return;
        }

        // mo populated_locality page
        $cat = $this->File_Categories->get_by_field('prefix', 'populated_locality');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array(
            'file_category_id' => $category_id,
            'alias' => $populated_locality_alias
        ), TRUE);

        if (!$post)
            show_404();

        $this->title = array_get($post, 'name', $this->title);
        $filters['populated_locality_id'] = (int) array_get($post, 'object_id');

        if (!($populated_locality = $this->Populated_Locality_Model->search(array('populated_locality_id' => $filters['populated_locality_id'], 'status' => Populated_Locality_Model::STATUS_ACTIVE), TRUE)))
            show_404();

        $this->breadcrumbs_add(array_get($populated_locality, 'name'), $url);

        $this->body = $this->load->view($this->template_dir . 'pages/catalog_geo_page', array('post' => $post), TRUE);
        // get objects
        $objects = $this->Object_Model->search($filters);
        $this->_render_result(array(), array('objects' => $objects, 'title' => $this->title, 'base_url' => $url));
    }

    public function geo_index() {

//        var_dump('ddddd');die;
        switch ($alias = $this->uri->segment(1)) {
            case 'novostrojki-po-okrugam-i-rajonam-moskvy':
                $this->title = 'Новостройки по округам и районам Москвы';
//                $alphabet = $this->Geo->get_alphabet('square');
                $data = array(
//                    'alphabet' => array_get($alphabet, 'alphabet', array()),
//                    'sub_nav' => array_get($alphabet, 'sub_nav', array()),
                    'title' => $this->title,
                    'path_url' => '/moskva/',
                    'build_url' => function(array $it) {
                        return '/moskva/' . array_get($it, 'parent_alias', 'vse-okruga') . '/' . array_get($it, 'alias');
                    },
                );
                break;
            case 'novostrojki-po-rajonam-moskovskoj-oblasti';
                $this->title = 'Новостройки по районам Московской области';
                $alphabet = $this->Geo->get_alphabet('geo_area_mos');
                $data = array(
                    'alphabet' => array_get($alphabet, 'alphabet', array()),
                    'title' => $this->title,
                    'path_url' => '/moskovskaya-oblast/',
                );
                break;
            case 'novostrojki-po-nas-punktam-moskovskoj-oblasti';
                $this->title = 'Новостройки по нас. пунктам Московской области';
//                $alphabet = $this->Geo->get_alphabet('populated_locality_mos');
                $data = array(
//                    'alphabet' => array_get($alphabet, 'alphabet', array()),
                    'title' => $this->title,
                    'path_url' => '/moskovskaya-oblast/',
                    'build_url' => function(array $it) {
                        return '/moskovskaya-oblast/' . array_get($it, 'parent_alias', 'vse-raiony') . '/' . array_get($it, 'alias');
                    },
                );
                break;
            case 'novostrojki-po-metro';
                $this->title = 'Новостройки по метро';
//                $alphabet = $this->Geo->get_alphabet('metro_station');
                $data = array(
//                    'alphabet' => array_get($alphabet, 'alphabet', array()),
                    'title' => $this->title,
                    'path_url' => '/' . $alias . '/',
                );
                break;
            default :
                show_404();
        }

        $this->body = $this->load->view($this->template_dir . 'pages/alphabet_list', $data, TRUE);

        $this->render();
    }

    public function metro_page($alias) {

        $this->message_no_found = 'Здесь пока нет новостроек';

        $cat = $this->File_Categories->get_by_field('prefix', 'metro_station');
        if (!($category_id = (int) array_get($cat, 'file_category_id')))
            show_404();

        $post = $this->Posts_Model->search(array('file_category_id' => $category_id, 'alias' => $alias), TRUE);

        if (!$post || !($id = (int) array_get($post, 'object_id')))
            show_404();

        $metro_station = $this->Metro_Station_Model->search(array($this->Metro_Station_Model->get_primary_key() => $id), TRUE);

        $this->title = $post['name'] = 'Новостройки у метро ' . array_get($post, 'name', $this->title);
        $this
                ->breadcrumbs_add('Новостройки по метро', '/' . $base_url = $this->uri->segment(1))
                ->breadcrumbs_add(array_get($metro_station, 'name', array_get($post, 'name')), $base_url = ($url . $alias . '/'));

        $this->body = $this->load->view($this->template_dir . 'pages/catalog_metro_page', ['post' => $post,], TRUE);
        $objects = $this->Object_Model->search(array_merge(array('metro_station_id' => (int) array_get($post, 'object_id')), $this->_get_order()));
        $this->_render_result([], array('objects' => $objects, 'title' => $this->title, 'base_url' => $base_url));
    }

    /**
     * get order
     * @return array - like $this->default_order
     */
    private function _get_order() {

        $get = $this->input->get();

        return array(
            'order' => array_get($get, 'sf', $this->default_order['order']),
            'order_direction' => array_get($get, 'sd', $this->default_order['order_direction'])
        );
    }

}
