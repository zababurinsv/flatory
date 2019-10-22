<?php

/**
 * Ajax controller
 * only ajax requests
 * @date 26.07.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Ajax extends CI_Controller {

    /**
     * Model
     * @var \Object_Model
     */
    public $Object_Model;

    /**
     *
     * @var \Metro_Station_Model
     */
    public $Metro_Station_Model;

    public function __construct() {
        parent::__construct();

        if ($this->input->server('HTTP_X_REQUESTED_WITH') !== 'XMLHttpRequest')
            redirect('/');

        $this->load->model('Geo');
        $this->load->model('Object_Model');
        $this->load->model('Metro_Station_Model');
    }

    public function subregion() {
        $success = FALSE;
        $data = array();

        // получаем данные запроса
        $zone = element('z', $_GET, FALSE);
        $value = element('v', $_GET, FALSE);

        if ($zone === FALSE && $value === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data, 'zone' => $zone));
            return FALSE;
        }
        // если есть такая гео зона в модели
        if (isset($this->Geo->zone[$zone])) {
            switch ($zone) {
                // для Мск запрашиваем square
                case 'MOW':
                    $data = $this->Geo->get_square($value);
                    $success = !empty($data);
                    break;
                default :
                    $data = $this->Geo->get_geo_area((int) $this->Geo->zone[$zone]->zone_id, (int) $value);
                    $success = !empty($data);
            }
        }

        echo json_encode(array('success' => $success, 'data' => $data, 'zone' => $zone));
        return FALSE;
    }

    public function geo_area() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $zone_id = element('zone_id', $get, FALSE);
        $geo_direction_id = element('geo_direction_id', $get, FALSE);

        if ($zone_id === FALSE && $geo_direction_id === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }

        $data = pluck_key_value($this->Geo->get_geo_area((int) $zone_id, (int) $geo_direction_id), 'geo_area_id', 'name');
        $success = !empty($data);

        echo json_encode(array('success' => $success, 'data' => $data));
        return FALSE;
    }

    public function populated_locality() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $zone_id = element('zone_id', $get, FALSE);
        $geo_direction_id = element('geo_direction_id', $get, FALSE);
        $geo_area_id = element('geo_area_id', $get, FALSE);

        if ($zone_id === FALSE && $geo_direction_id === FALSE && $geo_area_id === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }

        $data = pluck_key_value($this->Geo->get_populated_locality((int) $zone_id, (int) $geo_direction_id, (int) $geo_area_id), 'populated_locality_id', 'name');
        $success = !empty($data);

        echo json_encode(array('success' => $success, 'data' => $data));
        return FALSE;
    }

    public function square() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $district_id = element('district_id', $get, FALSE);

        if ($district_id === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }

        $data = pluck_key_value($this->Geo->get_square((int) $district_id), 'square_id', 'name');
        $success = !empty($data);

        echo json_encode(array('success' => $success, 'data' => $data));
        return FALSE;
    }

    public function metro_station() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $metro_name = element('metro_staition', $get, FALSE);

        if ($metro_name === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }
        $data = $this->Geo->get_metro_station_by_name($metro_name);
        $decorate_data = array();
        foreach ($data as $item) {
            $decorate_data[] = array(
                'label' => $item['name'],
                'value' => $item['metro_station_id'],
                'color' => $item['color'],
            );
        }
        $success = !empty($decorate_data);
        echo json_encode(array('success' => $success, 'data' => $decorate_data));
        return FALSE;
    }

    /**
     * Get objects by name
     * @return boolean
     */
    public function object_names() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $name = element('name', $get, FALSE);

        if ($name === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }
        $data = $this->Object_Model->get_by_name($name);
        $decorate_data = array();
        foreach ($data as $item) {
            $decorate_data[] = array(
                'label' => $item['name'],
                'value' => $item['id'],
            );
        }
        $success = !empty($decorate_data);
        echo json_encode(array('success' => $success, 'data' => $decorate_data));
        return FALSE;
    }

    /**
     * Get objects by zone
     * @return boolean
     */
    public function by_zone() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $zone = element('z', $get, FALSE);

        if ($zone === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }
        $data = $this->Object_Model->get_map_by_zone($zone);
        $success = !empty($data);
        echo json_encode(array('success' => $success, 'data' => $data));
        return FALSE;
    }

    /**
     * Get files
     */
    public function storage() {
        $this->load->model('Storage_Files');
        $this->load->library('flpagination');

        $get = xss_clean($_GET);
        $type = element('alias', $get, 'images');
        $page = (int) element('page', $get, 0);
        $limit = (int) element('limit', $get, 0);
        $offset = $page === -1 ? FALSE : $this->flpagination->get_offset($page, $limit);

        $files = $this->Storage_Files->get_by_file_type($type, 'alias', $get, $offset, FALSE, '', $limit);
        $total_rows = $this->Storage_Files->found_rows();

        echo json_encode(array('success' => !empty($files), 'data' => array(
                'files' => $files,
                'pagination' => array('current' => $page, 'limit' => $limit, 'total' => $total_rows),
                'input' => $get,
            ), 'errors' => array()));
        return FALSE;
    }

    public function geo_alphabet() {

        $tab = $this->input->get('t');
        $filters = [];

        switch ($tab) {
            // sync
            case '_msk':
                $type = 'square';
                break;
            case 'mo|regions':
                $type = 'geo_area_mos';
                break;
            case 'mo|cities':
                $type = 'populated_locality_mos';
                break;
            case 'new_msk|regions':
                $type = 'square';
                $filters['district_id'] = [10, 12];
                break;
            default :
                $type = '';
        }

        $data = $this->Geo->get_alphabet($type, $filters);
        $alphabet_list = array_get($data, 'alphabet', []);

        echo json_encode([
            'success' => !!$alphabet_list,
            'data' => $alphabet_list,
            'data_map' => array_get($data, 'data_map', []),
            'sub_nav' => array_get($data, 'sub_nav', []),
            'get' => $this->input->get()]);
    }

    public function object_search_map() {
//    {
//    "type": "FeatureCollection",
//    "features": [
//       {"type": "Feature", "id": 0, "geometry": {"type": "Point", "coordinates": [55.831903, 37.411961]}, "properties": {"balloonContent": "Содержимое балуна", "clusterCaption": "Еще одна метка", "hintContent": "Текст подсказки"}},
//      ]
//    }     

        $filters = $this->input->get();
        $objects = $this->Object_Model->search($filters);

        $features = [];

        foreach ($objects as $item) {
            $item = $this->Object_Model->prepare_object($item, [
                'truncate_list' => ['address' => 45, 'name' => 50],
                'image' => 'image_1',
                'cost' => ['cost_min'],
                'delivery' => TRUE,
            ]);

            $features[] = [
                'type' => 'Feature',
                'id' => array_get($item, 'id'),
                'geometry' => ['type' => 'Point', 'coordinates' => [array_get($item, 'y'), array_get($item, 'x')]],
                'properties' => $item,
            ];
        }

        echo json_encode(['success' => !!$features, 'data' => ['type' => 'FeatureCollection', 'features' => $features], 'filters' => $filters], JSON_UNESCAPED_UNICODE);
    }

    public function metro_stations_list() {
        $filters = $this->input->get();
        $stations = $this->Metro_Station_Model->search(is_array($filters) ? : $filters);
        echo json_encode(['success' => !!$stations, 'data' => $stations]);
    }

}
