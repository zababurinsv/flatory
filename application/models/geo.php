<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Модель работы с местоположением
 * @date 26.07.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Geo extends MY_Model {

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
     * @var \Metro_Station_Model
     */
    public $Metro_Station_Model;

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
    public $country;
    public $zone;
    public $limit = 10; // pagination offset
    public $status = array('2' => 'Черновик', '1' => 'Опубликовано');
    public $count;
    private static $_alphabet = [];

    public function __construct() {
        parent::__construct();
        // get current country
        $country_iso_2 = $this->config->item('iso_2');
        $this->country = $this->db->get_where('country', array('iso_code_2' => $country_iso_2))->row();
        // get default zone
        $zone_default = $this->config->item('zone_default');
        $this->db->select('*');
        $this->db->from('zone');
        $this->db->where('country_id', $this->country->country_id);
        $this->db->where_in('code', $zone_default);
        $this->zone = simple_tree_objects($this->db->get()->result(), 'code');
    }

    /**
     * Get locality
     * @param string $zone_code
     * @param array $direction - id of directions
     * @return array of objects
     */
    public function get_locality($zone_code, $direction = array()) {

        if (!isset($this->zone[$zone_code]))
            return array();

        $this->db->select('pl.*, plt.short_name');
        $this->db->from('populated_locality as pl');
        $this->db->join('populated_locality_type as plt', 'plt.populated_locality_type_id = pl.populated_locality_type_id', 'left');
        $this->db->where('zone_id', $this->zone[$zone_code]->zone_id);

        if (!empty($direction)) {

            if (is_array($direction))
                $this->db->where_in('geo_direction_id', $direction);

            if (is_numeric($direction))
                $this->db->where('geo_direction_id', $direction);
        }

        $locality = $this->db->get()->result();

        // MSK 
        if ($zone_code === 'MOW') {
            $locality = $this->get_districts($locality[0]);
        }

        return $locality;
    }

    /**
     * Get populated locality
     * @todo int /array ($geo_direction_id, $geo_area_id, $populated_locality_type_id)
     * @param int $zone_id
     * @param int $geo_direction_id
     * @param int $geo_area_id
     * @param int $populated_locality_type_id
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get_populated_locality($zone_id, $geo_direction_id = FALSE, $geo_area_id = FALSE, $populated_locality_type_id = FALSE, $status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;

        $this->db->ar_found_rows = TRUE;
        $this->db->select('pl.*, IFNULL( plt.name, "" ) as populated_locality_type, IFNULL( d.name, "" ) as direction, IFNULL( a.name, "" ) as geo_area', FALSE);
        $this->db->from('populated_locality as pl');
        $this->db->join('populated_locality_type as plt', 'plt.populated_locality_type_id = pl.populated_locality_type_id', 'left');
        $this->db->join('geo_direction as d', 'd.geo_direction_id = pl.geo_direction_id', 'left');
        $this->db->join('geo_area as a', 'a.geo_area_id = pl.geo_area_id', 'left');
        $this->db->where('pl.zone_id', (int) $zone_id);
        $this->db->where('pl.status !=', self::STATUS_DELETED);
        if ($geo_direction_id)
            $this->db->where('pl.geo_direction_id', (int) $geo_direction_id);
        if ($geo_area_id)
            $this->db->where('pl.geo_area_id', (int) $geo_area_id);
        if ($populated_locality_type_id)
            $this->db->where('pl.populated_locality_type_id', (int) $populated_locality_type_id);

        if (is_numeric($status))
            $this->db->where('pl.status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    /**
     * Get districts
     * @param object $locality
     * @return array of objects
     */
    public function get_districts($locality) {
        $districts = $this->db->get_where('district', array('populated_locality_id' => (int) $locality->populated_locality_id))->result();
        return $districts;
    }

    /**
     * get_districts_list
     * @param int $populated_locality_id
     * @param int $status
     * @param int $offset
     * @param string $order
     * @param string $order_direction
     * @param int $limit
     * @return array
     */
    public function get_districts_list($populated_locality_id = 1, $status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {

        $limit = !$limit ? $this->limit : $limit;
        $this->db->ar_found_rows = TRUE;
        $this->db->select('*');
        $this->db->from('district');
        $this->db->where('populated_locality_id', $populated_locality_id);
        $this->db->where('status !=', self::STATUS_DELETED);

        if (is_numeric($status))
            $this->db->where('status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    /**
     * Get directions
     * @return array of objects
     */
    public function get_directions() {
        return $this->db->get('geo_direction')->result();
    }

    /**
     * Get squares
     * @param array/int $districts - id of $districts
     * @return array of objects
     */
    public function get_square($districts, $status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;
        $this->db->ar_found_rows = TRUE;
        $this->db->select('sq.*, IFNULL( di.name, "" ) as district', FALSE);
        $this->db->from('square as sq');
        $this->db->join('district as di', 'di.district_id = sq.district_id', 'left');
        $this->db->where('sq.status !=', self::STATUS_DELETED);
        // по одному району
        if (is_numeric($districts) && $districts > 0)
            $this->db->where('sq.district_id', $districts);
        // по группе районов $districts - массив district_id 
        if (is_array($districts) && !empty($districts))
            $this->db->where_in('sq.district_id', $districts);

        if (is_numeric($status))
            $this->db->where('sq.status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }
        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    /**
     * Get geo area
     * @param int $zone_id
     * @param int $geo_direction_id
     * @return array if objects
     */
    public function get_geo_area($zone_id, $geo_direction_id = FALSE, $status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $t = 'a';
        $limit = !$limit ? $this->limit : $limit;
        $this->db->ar_found_rows = TRUE;
        $this->db->select($t . '.*, IFNULL( d.name, "" ) as direction', false);
        $this->db->where($t . '.status !=', self::STATUS_DELETED, false);
        $this->db->from('geo_area as ' . $t);
        if (is_numeric($geo_direction_id) && $geo_direction_id > 0)
            $this->db->where('a.geo_direction_id', $geo_direction_id);

        $this->db->join('geo_direction as d', $t . '.geo_direction_id = d.geo_direction_id', 'left');

        if (is_numeric($status))
            $this->db->where($t . '.status', $status, false);

        $this->db->where('zone_id', $zone_id);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    public function get_by_id($table, $id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($table . '_id', (int) $id);
        return $this->db->get()->row_array();
    }

    public function update_by_id($table, $id, $data) {
        $this->db->where($table . '_id', $id);
        return $this->db->update($table, $data);
    }

    public function insert($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function get_populated_locality_type($status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;
        $table = 'populated_locality_type';
        $this->db->ar_found_rows = TRUE;
        $this->db->select('*');
        $this->db->from($table);
        
        $this->db->where('status !=', self::STATUS_DELETED);

        if (is_numeric($status))
            $this->db->where('status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    public function get_metro_line($status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;
        $table = 'metro_line';
        $this->db->ar_found_rows = TRUE;
        $this->db->select('*');
        $this->db->from($table);

        $this->db->where('status !=', self::STATUS_DELETED);
        
        if (is_numeric($status))
            $this->db->where('status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);
        return $result;
    }

    /**
     * 
     * @param type $metro_line_id
     * @param type $status
     * @param type $offset
     * @param type $order
     * @param type $order_direction
     * @param type $limit
     * @param array $filters
     * @return type
     */
    public function get_metro_station($metro_line_id = 0, $status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE, array $filters = []) {
        $limit = !$limit ? $this->limit : $limit;
        $table = 'metro_station as ms';
        $this->db->ar_found_rows = TRUE;
        $this->db->select('ms.*');
        $this->db->from($table);
        
        $this->db->where('status !=', self::STATUS_DELETED);

        $this->db->join('metro_station_metro_line as mlr', 'ms.metro_station_id = mlr.metro_station_id', 'left');

        if (is_numeric($metro_line_id) && $metro_line_id > 0)
            $this->db->where('mlr.metro_line_id', $metro_line_id);

        if (is_numeric($status))
            $this->db->where('ms.status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }
        
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $this->db->where('ms.metro_station_id', (int) $filters['name_like']);
            } elseif (is_string($filters['name_like'])) {
                $this->db->like('ms.name', $filters['name_like']);
            }
        }

        if ($order !== FALSE && in_array($order, ['metro_station_id', 'name', 'metro_line_id', 'status']))
            $this->db->order_by($order, $order_direction);

        $result = $this->db->group_by('metro_station_id')->get()->result_array();
                
        // получаем количество без лимита
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        $this->count = (int) element('FOUND_ROWS()', $count, 0);

        if($station_ids = array_keys(simple_tree($result, 'metro_station_id'))) {
            $lines = simple_tree_group($this->db
                        ->join('metro_line', 'metro_line.metro_line_id = metro_station_metro_line.metro_line_id', 'left')
                        ->where_in('metro_station_id', $station_ids)
                        ->get('metro_station_metro_line')->result_array(), 'metro_station_id');
        } else {
            $lines = [];
        }

        

        // get_lines
        foreach ($result as $key => $value) {
            $result[$key]['metro_lines'] = array_get($lines, array_get($value, 'metro_station_id'), []);
        }

        return $result;
    }

    /**
     * Get metro station by name
     * @param string $name
     * @return array
     */
    public function get_metro_station_by_name($name, $status = 1) {
        $table = 'metro_station as ms';
        $this->db->select('ms.*');
        $this->db->from($table);
        $this->db->like('ms.name', strval($name));
        if (is_numeric($status))
            $this->db->where('ms.status', $status);
        $result = $this->db->get()->result_array();
        return $result;
    }

    /**
     * Get metro station by ids
     * @param array $ids
     * @return array
     */
    public function get_metro_station_by_ids($ids) {
        $table = 'metro_station as ms';
        $this->db->select('ms.*');
        $this->db->from($table);
        $this->db->where_in('metro_station_id', $ids);
        $result = $this->db->get()->result_array();
        return $result;
    }

    /**
     * Is zone - check for isset current zone
     * @param string $zone_code
     * @return bool
     */
    public function is_zone($zone_code) {
        return in_array($zone_code, array_keys($this->zone));
    }

    /**
     * Get zone id by zone code (from default zone)
     * @param string $zone_code
     * @return int∕false
     */
    public function get_zone_id_by_code($zone_code) {
        if ($this->is_zone($zone_code))
            return (int) $this->zone[$zone_code]->zone_id;
        return FALSE;
    }

    /**
     * Get zone ids
     * @return array
     */
    public function get_zone_ids() {
        return array_keys(simple_tree_objects($this->zone, 'zone_id'));
    }

    /**
     * get geo alphabet
     * @param string $type:<br>
     * <b>square</b> - moscow square with districts<br>
     * <b>geo_area_mos</b> - mos obl geo_area<br>
     * <b>populated_locality_mos</b> - mos obl populated_locality<br>
     * <b>square_10</b> - new moscow<br>
     * <b>districts</b> - moscow districts<br>
     * <b>metro_station</b> - moscow metro station<br>
     * @param array $filters
     * @return array ['alphabet' => array, 'data_map' => array, 'sub_nav' => array]
     */
    public function get_alphabet($type, array $filters = []) {
        $data = $data_map = $sub_nav = [];

        if (array_get(self::$_alphabet, $type))
            return self::$_alphabet[$type];

        switch ($type) {
            // sync
            case 'square':
                $this->load_model('Square_Model');
                $this->load_model('District_Model');
                $data = $this->Square_Model->search(array_merge($filters, ['with' => ['posts', 'post_parent_alias'], 'order' => 'name', 'status' => Square_Model::STATUS_ACTIVE]));
                $data_map = $this->Square_Model->get_data_map();

                $districts = $this->District_Model->search(array_merge($filters, ['with' => ['posts'], 'order' => 'name', 'status' => District_Model::STATUS_ACTIVE]));

                foreach ($districts as $key => $value) {
                    $sub_nav[] = [
                        'label' => array_get($value, 'short_name', ''),
                        'value' => array_get($value, 'district_id', ''),
                        'field' => 'district_id',
                        'alias' => array_get($value, 'alias', ''),
                    ];
                }
                break;
            case 'geo_area_mos':
                $this->load_model('Geo_Area_Model');
                $data = $this->Geo_Area_Model->search(array_merge($filters, ['with' => ['posts'], 'zone_id' => $this->zone['MOS']->zone_id, 'order' => 'name', 'status' => Geo_Area_Model::STATUS_ACTIVE]));
                $data_map = $this->Geo_Area_Model->get_data_map();
                break;
            case 'populated_locality_mos':
                // Populated_Locality_Model
                $this->load_model('Populated_Locality_Model');
                $data = $this->Populated_Locality_Model->search(array_merge($filters, ['with' => ['posts', 'post_parent_alias'], 'zone_id' => $this->zone['MOS']->zone_id, 'order' => 'name', 'status' => Populated_Locality_Model::STATUS_ACTIVE]));
                $data_map = $this->Populated_Locality_Model->get_data_map();
                break;
            case 'districts':
                $this->load_model('District_Model');
                $data = $this->District_Model->search(['with' => ['posts'], 'order' => 'name', 'status' => District_Model::STATUS_ACTIVE]);
                $data_map = $this->District_Model->get_data_map();
                break;
            case 'metro_station':
                $this->load_model('Metro_Station_Model');
                $data = $this->Metro_Station_Model->search(['with' => ['posts'], 'order' => 'name', 'status' => Metro_Station_Model::STATUS_ACTIVE]);
                $data_map = $this->Metro_Station_Model->get_data_map();
                break;
        }

        $alphabet_list = $this->build_alphabet($data, $data_map);

        $data_map['count'] = count($alphabet_list);

        $result = [
            'alphabet' => $alphabet_list,
            'data_map' => $data_map,
            'sub_nav' => $sub_nav,
        ];

        self::$_alphabet[$type] = $result;

        return $result;
    }

    /**
     * build alphabet
     * @param array $data - list for alphabet
     * @param array $data_map - ['label' => (string)'fiel_name', 'value' => (string)'fiel_name']
     * @return array
     */
    public function build_alphabet(array $data, array $data_map) {
        if (!$data || !array_get($data_map, 'label') || !array_get($data_map, 'value'))
            return $data;

        $alphabet_list = [];

        foreach ($data as $key => $value) {
            $letter = mb_substr(array_get($value, $data_map['label'], ''), 0, 1);

            foreach ($data_map as $dk => $dv)
                $it[$dk] = array_get($value, $dv, '');

            $it['field'] = $data_map['value'];

            if (array_get($data_map, 'parent_id'))
                $it[$data_map['parent_id']] = array_get($value, $data_map['parent_id'], '');

            if (!isset($alphabet_list[$letter])) {
                $alphabet_list[$letter] = [
                    'letter' => $letter,
                    'items' => [$it],
                ];
            } else {
                $alphabet_list[$letter]['items'][] = $it;
            }
        }

        return $alphabet_list;
    }

    /**
     * update count_objects on geo handbks: square, geo_area, populated_locality, district, metro_station
     */
    public function handbks_object_counts_update() {
        $this->db->query("call handbks_object_counts_update();");
    }

}
