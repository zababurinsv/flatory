<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Модель для работы с поиском
 * @date 14.09.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Search_Model extends CI_Model {

    const THOUSAND = 1000;
    const MILLION = 1000000;

    // put here sorted array of objects_id : array((int)object_id => (int)sort_value)
    private $sorted_objects = array();

    function __construct() {
        $this->load->model('Geo');
        $this->load->model('Object_Model');
    }

    public function Search_Model() {
        parent::Model();
    }

    /**
     * Search by flats  - get objects_id by rooms, space, cost_m, cost (min & max)
     * @param array $params - $_GET
     * @return array - object_id
     * 
     * @example SQL query :
     * -- max 200
     * -- min 10
     * SELECT `object_id` FROM `cat_flats` WHERE 
     * `room_id` IN (1,2) 
     * AND `space_min` <= 200 
     * AND (`space_max` >= 10 OR (`space_min` > 0 AND `space_max` = 0)) -- unlimit max
     * AND `cost_m_min` <= 200 
     * AND (`cost_m_max` >= 10 OR (`cost_m_min` > 0 AND `cost_m_max` = 0)) -- unlimit max
     * AND `cost_min` <= 200 
     * AND (`cost_max` >= 10 OR (`cost_min` > 0 AND `cost_max` = 0)) -- unlimit max
     * GROUP BY `object_id`
     * ORDER BY `cost_m_min` ASC;
     */
    public function search_by_flats($params) {

        // prepare data
        $rooms = element('rooms', $params, array());
        $space_min = (int) element('space_min', $params, 0);
        $space_max = (int) element('space_max', $params, 0);
        $cost_m_min = $this->generate_value(element('cost_m_min', $params, 0), self::THOUSAND);
        $cost_m_max = $this->generate_value(element('cost_m_max', $params, 0), self::THOUSAND);
        $cost_min = $this->generate_value(element('cost_min', $params, 0), self::MILLION);
        $cost_max = $this->generate_value(element('cost_max', $params, 0), self::MILLION);

        // sort_by & sort_direction
        $sort_by = element('sort_by', $params, false);
        $sort_direction = element('sort_direction', $params, 'asc');
        $sort_direction = $sort_direction === 'asc' ? 'asc' : 'desc';

        $this->db->select('object_id');
        if (!empty($rooms))
            $this->db->where_in('room_id', $rooms);
        // space_min
        $this->db->where('space_min <=', $space_max);
        // space_max with unlimit max
        $where_space_max = '(`space_max` >= ' . $space_min . ' OR (`space_min` > 0 AND `space_max` = 0))';
        $this->db->where($where_space_max, NULL, FALSE);
        // cost_m_min
        $this->db->where('cost_m_min <=', $cost_m_max);
        // cost_m_max with unlimit max
        $where_cost_m_max = '(`cost_m_max` >= ' . $cost_m_min . ' OR (`cost_m_min` > 0 AND `cost_m_max` = 0))';
        $this->db->where($where_cost_m_max, NULL, FALSE);
        // cost_min
        $this->db->where('cost_min <=', $cost_max);
        // cost_max with unlimit max
        $where_cost_max = '(`cost_max` >= ' . $cost_min . ' OR (`cost_min` > 0 AND `cost_max` = 0))';
        $this->db->where($where_cost_max, NULL, FALSE);
        // group by
        $this->db->group_by('object_id');
        // sort
        $flats_fields = array('room_id', 'space_min', 'space_max', 'cost_m_min', 'cost_m_max', 'cost_min', 'cost_max');
        $is_sorted = $sort_by !== FALSE && in_array($sort_by, $flats_fields);
        if ($is_sorted)
            $this->db->order_by($sort_by, $sort_direction);
        // get
        $result = $this->db->get('flats')->result_array();
        if (!empty($result))
            $result = simple_tree($result, 'object_id');

//        vdump($this->db->last_query(), 1);
        // set sorted objects
//        if ($is_sorted)
        $this->set_sorted_objects($result);

        return $result;
    }

    /**
     * Generate value
     * @param int $int - decorating value
     * @param int $multipl - multipl for value
     * @return int
     */
    public function generate_value($int, $multipl) {
        return (int) $int = (int) $int !== 0 ? $int * $multipl : $int;
    }

    /**
     * Set sorted objects
     * @param array $objects - array search result
     */
    public function set_sorted_objects($objects) {
        $sorted = array();
        $i = 1;
        foreach ($objects as $object_id => $item) {
            $sorted[$object_id] = $i++;
        }
        $this->sorted_objects = $sorted;
    }

    /**
     * Get sorted objects
     * @return array
     */
    public function get_sorted_objects() {
        return $this->sorted_objects;
    }

    /**
     * Sort objects
     * @param array $objects
     * @return array
     */
    public function sort_objects($objects) {
        $sorted = array();
        $sorted_objects = $this->get_sorted_objects();

        foreach ($objects as $key => $object) {
            $object_id = (int) element('id', $object, 0);
            // if current object _id included in sorted_object
            // add with this sort index in $sorted && unset current object
            if (array_key_exists($object_id, $sorted_objects)) {
                $sorted[$sorted_objects[$object_id]] = $object;
                unset($objects[$key]);
            }
        }
        ksort($sorted);

        if (!empty($objects))
            foreach ($objects as $object) {
                $sorted[] = $object;
            }

        return $sorted;
    }

    /**
     * Get max filters value (rount top)
     * @return array
     */
    public function get_max_filters($zone_ids) {

        $zone_ids = is_array($zone_ids) ? implode(',', $zone_ids) : (int) $zone_ids;

        $query = 'SELECT `cm`.`zone_id`,
            CEIL(max(`f`.`space_max`)/10)*10 AS `space_max`, 
            CEIL(CEIL(max(`f`.`cost_m_max`) / 1000 )/10)*10 AS `cost_m_max`,  
            CEIL(CEIL(max(`f`.`cost_max`) / 1000000)/10)*10 AS `cost_max`,
            (SELECT CEIL(max(`floor_end`)/10)*10 FROM `' . $this->db->dbprefix . 'meta` AS `cmf` WHERE `cmf`.`zone_id` = `cm`.`zone_id`) AS `floor_max`,
            `cm`.`zone_id`, `z`.`code`
            FROM `' . $this->db->dbprefix . 'flats` AS `f`
            LEFT JOIN `' . $this->db->dbprefix . 'meta` AS `cm` ON (`cm`.`id_object` = `f`.`object_id`)
            LEFT JOIN `' . $this->db->dbprefix . 'zone` AS `z` USING (`zone_id`)
            WHERE `cm`.`zone_id` IN (' . $zone_ids . ')
            GROUP BY `cm`.`zone_id`;';

        $result = $this->db->query($query)->result_array();

        if (!empty($result))
            $result = simple_tree($result, 'code');

        return $result;
    }

    /**
     * Search by delivery date
     * @param array $params - GET
     * @return array - object_id in key
     */
    public function seach_by_delivery_date($params) {
        $table = 'cat_delivery';
        // prepare data
        $year_start = date("Y", time());
        $year_and = date("Y", time()) + 1;
        $complite_min = (int) element('complite_min', $params, 0);
        $complite_max = (int) element('complite_max', $params, 9);

        // min
        $quarter_min = $complite_min % 4 === 0 ? 4 : $complite_min % 4;
        $year_min = $complite_min > 4 ? $year_and : $year_start;
        // max
        $quarter_max = $complite_max % 4 === 0 ? 4 : $complite_max % 4;
        $year_max = $complite_max > 4 ? $year_and : $year_start;

        // no result
        if ($complite_min < 0 || $complite_max < 0 || $complite_min > 9 || $complite_max > 9)
            return array();
        // если фильтр выставлен в максимальные значения, то фильтр не работает
        if ($complite_min === 0 && $complite_max === 9)
            return array();

        // оба маркера на одном максимуме!
        if ($complite_min === $complite_max && ($complite_min === 0 || $complite_min === 9)) {
            if ($complite_min === 0)
                $sql = 'SELECT `object_id` FROM ' . $table . ' WHERE `year` < ' . $year_start . ';';
            else
                $sql = 'SELECT `object_id` FROM ' . $table . ' WHERE `year` > ' . $year_and . ';';
            $result = $this->db->query($sql)->result_array();
            if (!empty($result))
                $result = simple_tree($result, 'object_id');
            return $result;
        }
        // один маркер стоит на максимуме а другой нет 
        if ($complite_min === 0) {
            // максимум - 0
            $sql = 'SELECT `object_id` FROM ' . $table . '
                    WHERE (`year` = ' . $year_max . ' AND `quarter` <= ' . $quarter_max . ') OR `year` < ' . $year_max . ';';
            $result = $this->db->query($sql)->result_array();
            if (!empty($result))
                $result = simple_tree($result, 'object_id');
            return $result;
        } elseif ($complite_max === 9) {
            // максимум - 9
            $sql = 'SELECT `object_id` FROM ' . $table . '
                    WHERE (`year` = ' . $year_min . ' AND `quarter` >= ' . $quarter_min . ') OR `year` > ' . $year_min . ';';
            $result = $this->db->query($sql)->result_array();
            if (!empty($result))
                $result = simple_tree($result, 'object_id');
            return $result;
        }

        // ни один из маркеров не установлен на максимум
        // маркеры имеют разные года
        if ($year_min !== $year_max)
            $sql = 'SELECT `object_id` FROM ' . $table . '
                WHERE (`year` = ' . $year_max . ' AND `quarter` <= ' . $quarter_max . ') OR
                (`year` = ' . $year_min . ' AND `quarter` >= ' . $quarter_min . ')';
        else
            $sql = 'SELECT `object_id` FROM ' . $table . '
                    WHERE `year` = ' . $year_min . ' AND `quarter` <= ' . $quarter_max . '
                    AND `quarter` >= ' . $quarter_min . ';';
        $result = $this->db->query($sql)->result_array();
        if (!empty($result))
            $result = simple_tree($result, 'object_id');
        return $result;
    }

    /**
     * Поиск id объектов
     * @param array $get - фильтры
     * @return array
     */
    public function search_object_ids($get) {
        // Определяем регион
        // по умолчанию всегда Москва (даже если передан неверный параметр)
        $zone_code = element('code', $get, array('MOS'));
        foreach ($zone_code as $key => $zc) {
            if (!$this->Geo->is_zone($zc))
                unset($zone_code [$key]);
        }
        $zone_code = implode('', $zone_code);

        // search by name
        if (($name = array_get($get, 'name', FALSE)) !== FALSE) {
            $objects = array();
            if ($name)
                $objects = $this->Object_Model->get_by_name($name);

            if (!empty($objects))
                $objects = simple_tree($objects, 'id');

            // не нашли ничего по низванию или регион не определен
            if ((empty($objects) && count($get) === 1 ) || !$zone_code) {
                return array();
            }

            // если найден один объект по названию - остальные фильтры не смотрим
            if (count($objects) === 1) {
                return $objects;
            }
        }

        // максимумы фильтров
        $max_filters = $this->get_max_filters($this->Geo->get_zone_ids());

        if (strlen($zone_code) > 3) {
            $general_max_filters = array();
            foreach ($max_filters['MOS'] as $k => $v) {
                $general_max_filters[$zone_code][$k] = max(element($k, $max_filters['MOW'], 0), $v);
            }
            $max_filters = $general_max_filters;
        }

        // По табице META
        $this->db->select('id_object');
        $this->db->from('meta');

        // floors
        $floor_min = element('floor_min', $get, 0);
        $floor_max = element('floor_max', $get, 0);
        if (($floor_min > 0 || $floor_max > 0) && ($floor_min !== 0 || $floor_max !== element('floor_max', $max_filters[$zone_code], 0))) {
            // попадают все объекты при: минимум фильтра меньше или равен максимуму объекта (do)
            $this->db->where('floor_end >=', $floor_min);
        }

        // MOW поиск по Москве 
        if ($zone_code === 'MOW') {
            // округ
            if (($district_id = (int) element('district', $get, 0)) > 0)
                $this->db->where('district_id', $district_id);
            // район
            if (($square_id = (int) element('square', $get, 0)) > 0)
                $this->db->where('square_id', $square_id);

            // метро
            if (!is_numeric(element('metro_staition_id', $get, 0))) {
                // если прилетает строка - значит ид метро не был найден при автокомплите
                // поэтому результат поиска - пустой
                return array();
            }
            if (($metro_staition_id = (int) element('metro_staition_id', $get, 0)) > 0) {
                $this->db->where_find_in_set('metro_station_ids', $metro_staition_id);
            }
            // zone
            $this->db->where('zone_id', $this->Geo->get_zone_id_by_code($zone_code));
        } elseif ($zone_code === 'MOS') {
            // MOS поиск по области
            // направление
            if (($geo_direction = (int) element('geo_direction', $get, 0)) > 0)
                $this->db->where('geo_direction_id', $geo_direction);
            // район
            if (($populated_locality = (int) element('geo_area', $get, 0)) > 0)
                $this->db->where('geo_area_id', $populated_locality);
            // distance to MKAD и если не выбран пункт "Не важно" (0)
            if (($distance_to_mkad = (int) element('distance_to_mkad', $get, 0)) > 0)
                $this->db->where('id_distance_to_mkad', $distance_to_mkad);
            // zone
            $this->db->where('zone_id', $this->Geo->get_zone_id_by_code($zone_code));
        } elseif (!$zone_code && !$name) {
            // нет региона, при этом идет поиск по названию
            // поэтому результат поиска - пустой
            return array();
        }

        // Отделка
        // При выборе "не важно" - все объекты, 
        // "с отделкой" - объекты с отделкой и объекты со смешанным типом(с отделкой/без отделки), 
        // "без отделки" - объекты без отделки и смешанного типа
        if (($furnish = (int) element('furnish', $get, 0)) > 0) {
            $condition = '(id_furnish = ' . $furnish . ' OR id_furnish = 3)';
            $this->db->where($condition);
        }

        $result = $this->db->get()->result_array();

        if (!empty($result))
            $result = simple_tree($result, 'id_object');

//            vdump($this->db->last_query(), 1);
//            vdump($metro_staition_id, 1);
//            vdump((int)element('transport_type', $get, 0),1);
        // Удаленность от метро только для MOW (Москва)
        // Если выбрана станция метро, то удаленность считается относительно указанной станции.
        // Если станция не выбрана, то считается удаленность относительно любой станции
        // Если выбран пункт "Не важно" (0) - поиск не производится
        $remotnes_metro_objects_ids = array();
        if ($zone_code === 'MOW' && (int) element('transport_type', $get, 0) > 0) {
            $remoteness_min = (float) element('remoteness_min', $get, 0);
            $remoteness_max = (float) element('remoteness_max', $get, 0);

            $remotnes_metro_objects_ids = $this->db->select('object_id')
                    ->where('distance >=', $remoteness_min)
                    ->where('distance <=', $remoteness_max);
            // указана станция
            if (isset($metro_staition_id) && $metro_staition_id > 0)
                $remotnes_metro_objects_ids->where('metro_id', $metro_staition_id);

            $remotnes_metro_objects_ids = $remotnes_metro_objects_ids->get('meta_metro')
                    ->result_array();

//                vdump($this->db->last_query(), 1);

            if (!empty($remotnes_metro_objects_ids))
                $remotnes_metro_objects_ids = simple_tree($remotnes_metro_objects_ids, 'object_id');
        }
        // serch by flats (get objects_id by rooms, space, cost_m, cost)
        $flats_objects = $this->search_by_flats($get);
        if (empty($flats_objects))
            return array();
//        vdump($this->db->last_query(), 1);
//        vdump($flats_objects, 1);
        // search by object complete
        $delivery_objects = $this->seach_by_delivery_date($get);
//        vdump($this->db->last_query(), 1);
        // сверяем два списка id объектов основной фильтр и результат поиска по названию
        if (!empty($objects))
            $result = array_intersect_key($result, $objects);
        // сверяем два списка id объектов основной фильтр и количество комнат
        if (!empty($flats_objects))
            $result = array_intersect_key($result, $flats_objects);
        // сверяем два списка id объектов основной фильтр и срок ввода
        if (!empty($delivery_objects))
            $result = array_intersect_key($result, $delivery_objects);
        // сверяем два списка id объектов основной фильтр и удаленность от метро
        if (!empty($remotnes_metro_objects_ids))
            $result = array_intersect_key($result, $remotnes_metro_objects_ids);

        return $result;
    }

    /**
     * Search object ids
     * @param array $filters
     * @return array
     */
    public function search_ids(array $filters = []) {

        foreach ($filters as $key => $value) {
            if (!is_numeric($value) && !$value)
                unset($filters[$key]);
        }

        $filters_keys = array_keys($filters);

        if (array_intersect($filters_keys, ['cost_min', 'cost_max', 'rooms',])) {
            if (!($flats_objects = $this->search_ids_by_flats($filters)))
                return [];
        }

        if (in_array('complite', $filters_keys)) {
            if (!($delivery_objects = $this->search_ids_by_delivery($filters)))
                return [];
        }

        $conditions = $joins = [];

        if (array_get($filters, 'name'))
            $conditions[] = "AND mo.name LIKE '%" . $this->db->escape_like_str(strval($filters['name'])) . "%'";

        $geo_index = is_array(array_get($filters, 'geo_index')) ? $filters['geo_index'] : [];

        if (array_get($geo_index, 'district_id'))
            $conditions[] = "AND district_id IN (" . implode_int(',', $geo_index['district_id']) . ')';
        
        if (array_get($geo_index, 'square_id'))
            $conditions[] = "AND square_id IN (" . implode_int(',', $geo_index['square_id']) . ')';
        
        if (array_get($geo_index, 'geo_area_id'))
            $conditions[] = "AND geo_area_id IN (" . implode_int(',', $geo_index['geo_area_id']) . ')';
        
        if (array_get($geo_index, 'populated_locality_id'))
            $conditions[] = "AND populated_locality_id IN (" . implode_int(',', $geo_index['populated_locality_id']) . ')';
                
        // cat_meta_metro
        if (array_get($geo_index, 'metro_station_id')) {
            $joins[] = " LEFT JOIN ". $this->db->dbprefix ."meta_metro AS ms_o ON ms_o.object_id = mo.id ";
            if (is_array($geo_index['metro_station_id'])) {
                $conditions[] = 'AND ms_o.metro_id IN(' . implode_int(',', $geo_index['metro_station_id']) . ')';
            } else {
                $conditions[] = 'AND ms_o.metro_id = ' . (int) $geo_index['metro_station_id'];
            }
        }

        $conditions = !!$conditions ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';
        $joins = !!$joins ? implode(' ', $joins) : '';

        $result = $this->db->query("SELECT id_object as object_id FROM " . $this->db->dbprefix . 'meta AS m'
                        . ' LEFT JOIN  ' . $this->db->dbprefix . 'main_object AS mo ON mo.id = m.id_object ' . $joins . ' ' . $conditions)->result_array();

        if (!!$result)
            $result = simple_tree($result, 'object_id');
        
//        vdump($this->db->last_query());

        // сверяем два списка id объектов основной фильтр и [стоимость, комнатность]
        if (isset($flats_objects) && $flats_objects)
            $result = array_intersect_key($result, $flats_objects);
        
        if (isset($delivery_objects) && $delivery_objects)
            $result = array_intersect_key($result, $delivery_objects);
        
        return $result;
    }

    /**
     * Search object ids by flats
     * @param array $filters
     * @return array
     */
    public function search_ids_by_flats(array $filters = []) {

        // sort_by & sort_direction
        $sort_by = array_get($filters, 'sort_by', false);
        $sort_direction = array_get($filters, 'sort_direction', 'asc');
        $sort_direction = $sort_direction === 'asc' ? 'asc' : 'desc';

        $this->db->select('object_id');

        if (is_array($rooms = array_get($filters, 'rooms')) && !!$rooms) {
            // 4+
            if (in_array('4', $rooms))
                $rooms[] = '5';
            $this->db->where_in('room_id', $rooms);
        }

        $price_type = (int) array_get($filters, 'price_type');

        // cost_min
        if (array_get($filters, 'cost_min')) {
            $cost_min = (int)str_replace(' ', '', strval($filters['cost_min']));
            // price_type = 1 => cost_m_min || price_type = 0 => cost_min
            !!$price_type ?
                            $this->db->where('(`cost_m_max` >= ' . $cost_min . ' OR (`cost_m_min` > 0 AND `cost_m_max` = 0))', NULL, FALSE) :
                            $this->db->where('(`cost_max` >= ' . $cost_min . ' OR (`cost_min` > 0 AND `cost_max` = 0))', NULL, FALSE);
        }

        // cost_max
        if (array_get($filters, 'cost_max')) {
            $cost_max = (int)str_replace(' ', '', strval($filters['cost_max']));
            // price_type = 1 => cost_m_max || price_type = 0 => cost_max
            !!$price_type ? $this->db->where('cost_m_min <=', $cost_max) : $this->db->where('cost_min <=', $cost_max);
        }

        // group by
        $this->db->group_by('object_id');
        // sort
        $flats_fields = array('room_id', 'space_min', 'space_max', 'cost_m_min', 'cost_m_max', 'cost_min', 'cost_max');
        $is_sorted = $sort_by !== FALSE && in_array($sort_by, $flats_fields);
        if ($is_sorted)
            $this->db->order_by($sort_by, $sort_direction);
        // get
        $result = $this->db->get('flats')->result_array();
        if ($result)
            $result = simple_tree($result, 'object_id');

//        vdump($this->db->last_query(), 1);
        // set sorted objects
//        if ($is_sorted)
        $this->set_sorted_objects($result);

        return $result;
    }

    /**
     * Search object ids by delivery date
     * @param array $filters
     * @return array
     */
    public function search_ids_by_delivery(array $filters = []) {

        $conditions = '';

        if (is_array($complite = array_get($filters, 'complite')) && !!$complite) {
            $last_year = ($curr_year = date("Y")) + 2;
            
            if (($is_complite = array_search('1', $complite)) !== FALSE) {
                unset($complite[$is_complite]);
                $conditions .= "AND (year = '". $curr_year ."' OR (year < " . $curr_year . " AND year > 0)) ";
            }

            if ($complite) {
                if (in_array($last_year, $complite)) {
                    $conditions .= "AND (year IN(" . implode_int(',', $complite) . ") OR year > " . $last_year . ") ";
                } else {
                    $conditions .= "AND year IN(" . implode_int(',', $complite) . ") ";
                }
            }
        }
        
        if(!!($conditions = ltrim($conditions, 'AND')))
            $conditions = "WHERE " . ltrim($conditions, 'AND');
        
        $sql = 'SELECT `object_id` FROM ' . $this->db->dbprefix . 'delivery ' . $conditions;
        $result = $this->db->query($sql)->result_array();

        if (!!$result)
            $result = simple_tree($result, 'object_id');

        return $result;
    }

}
