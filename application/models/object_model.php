<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Модель для работы с объектами (жилые комплексы)
 * @date 07.09.2014
 * @todo rewrite
 */
class Object_Model extends MY_Model {

    // текущий объект
    private $object = array();
    private $image = array();
    public $image_path_middle = '/images/570x365/';
    public $image_path_full = '/images/1140x730/';

    /**
     * Model
     * @var \File_Categories 
     */
    public $File_Categories;

    /**
     * Model
     * @var \Storage_Files
     */
    public $Storage_Files;
    /**
     * Model
     * @var \Registry_Model
     */
    public $Registry_Model;
    /**
     * Model
     * @var \Handbks_Model
     */
    public $Handbks_Model;

    public function __construct() {
        parent::__construct();

        $this->table = 'main_object';
        $this->primary_key = 'id';

        $this->load_model('Flats');
        $this->load_model('Geo');
        $this->load_model('Storage_Files');
        $this->load_model('Proportions');
        $this->load_model('Image_Albums');
        $this->load_model('File_Categories');
        $this->load_model('Registry_Model');
        $this->load_model('Handbks_Model');
        $this->load_model('Links');
    }

    /**
     * 
     * @return array
     */
    public function get_by_alias($alias) {

//var_dump('object_models');die;
        $this->object = $this->db
                        ->select('mo.*, m.*, '
                                . 'z.code, z.name as zone, '
                                . 'd.name as district, s.name as square, '
                                . 'dd.name as geo_direction, ga.name as geo_area, pl.name as populated_locality,'
                                . 'dmk.name as distance_to_mkad,'
                                . 'm.point, m.bus, m.auto,'
                                . 'm.garage_comment, m.protection_comment,'
                                . 'm.floor_begin, m.floor_end,'
                                . 'm.number_of_sec,'
                                . 'g.name as garage,'
                                . 'p.name as protection,'
                                . 'sb.name as state_building,'
                                . 'm.infrastructure,'
                        )
                        ->from('main_object as mo')
                        ->join('meta as m', 'm.id_object = mo.id', 'left')
                        ->join('zone as z', 'm.zone_id = z.zone_id', 'left')
                        ->join('district as d', 'm.district_id = d.district_id', 'left')
                        ->join('square as s', 'm.square_id = s.square_id', 'left')
                        ->join('geo_direction as dd', 'm.geo_direction_id = dd.geo_direction_id', 'left')
                        ->join('geo_area as ga', 'm.geo_area_id = ga.geo_area_id', 'left')
                        ->join('populated_locality as pl', 'm.populated_locality_id = pl.populated_locality_id', 'left')
                        ->join('distance_to_mkad as dmk', 'm.id_distance_to_mkad = dmk.id', 'left')
                        ->join('garage as g', 'm.id_garage = g.id', 'left')
                        ->join('protection as p', 'm.id_protection = p.id', 'left')
                        ->join('state_building as sb', 'm.id_state_building = sb.id', 'left')
                        ->where('alias', $alias)
                        ->where('mo.status !=', self::STATUS_DELETED )
                        ->get()->row_array();


//        vdump($this->db->last_query());
        // distance to metro with metro station
        $this->object['metro'] = $this->metro();

//        vdump($this->db->last_query(), 1);
        // flats
        $this->object['flats'] = $this->flats();
        // ceiling_height
        $this->object['ceiling_height'] = array_keys(simple_tree($this->db->where('object_id', $this->id())->select('ceiling_height')->get('meta_ceiling_height')->result_array(), 'ceiling_height'));
        // commissioning_date
        $this->object['delivery'] = $this->get_delivery();
        // type of building
        $this->object['type_of_building'] = $this->type_of_building();
        // building_lot
        $this->object['building_lot'] = $this->building_lot();
        // cost
        $this->object['cost'] = $this->cost();
        // space
        $this->object['space'] = $this->space();
        // pluns
        $this->object['pluns'] = $this->pluns();
        // documents
        $docs = $this->documents();
        $this->object['documents'] = element('files', $docs);
        $this->object['documents_links'] = element('links', $docs);
        // infrastructure_images
        $this->object['infrastructure_images'] = $this->infrastructure_images();


        $this->load_model('Organizations_Model');
        $organizations = simple_tree_group($this->Organizations_Model->get_by_object($this->id()), 'organization_type');

        // sellers
        $this->object['sellers'] = element('Продавец', $organizations, array());
        // builders
        $this->object['builders'] = element('Застройщик', $organizations, array());
        // images
        // @todo get file_category_id
        $this->image = $this->Storage_Files->get_by_category(2, $this->id());

//        vdump($this->db->last_query(), 1);

        return $this;
    }

    /**
     * Все данные об объекте
     * @return array
     */
    public function object() {
        return $this->object;
    }

    /**
     * Идентификатор объекта
     * @return int
     */
    public function id() {
        return (int) element('id_object', $this->object, 0);
    }

    /**
     * Изображения
     * @return array
     */
    public function images() {
        return $this->image;
    }

    /**
     * Метро
     * @return array
     */
    public function metro() {

        $sql = 'select d.*, ms.metro_station_id, ms.name as metro_station '
                . 'from cat_meta_metro as d '
                . 'left join cat_metro_station as ms ON (d.metro_id = ms.metro_station_id) '
                . 'where object_id = ' . $this->id() . ' '
                . 'order by if(walking_time = "" or walking_time is null,1,0), walking_time , if(drive_time = "" or drive_time is null,1,0), drive_time;';

        $result = $this->db->query($sql)->result_array();

//        vdump($result, 1);

        if ($result) {
            $this->load_model('Metro_Station_Model');
            $result = simple_tree($result, 'metro_station_id');
            $lines = $this->Metro_Station_Model->get_station_lines(array_keys($result));

//            vdump($this->db->last_query(), 1);

            foreach ($lines as $line) {
                $metro_station_id = array_get($line, 'metro_station_id');
                if (isset($result[$metro_station_id]['metro_lines']))
                    $result[$metro_station_id]['metro_lines'][] = $line;
                else
                    $result[$metro_station_id]['metro_lines'] = [$line];
            }
//            vdump($result, 1);
//            vdump($lines);
        }

        return $result;
    }

    /**
     * Данные о квартирах объекта 
     * (комнатность, площадь, стоимость, стоимость за метр)
     * @return array
     */
    public function flats() {
        // ['cost_exitst' => TRUE]
        return $this->Flats->get_flats_by_object_id($this->id());
    }

    /**
     * Сроки ввода
     * @return array
     */
    public function get_delivery() {
        return $this->db->where('object_id', $this->id())->get('delivery')->row_array();
    }

    /**
     * Типы зданий объекта
     * @return array
     */
    public function type_of_building() {
        $prefix = $this->get_table_prefix();

        $sql = "SELECT ro.object_id, registry_id, r.name, r.handbk_id, g.alias
            FROM {$prefix}registry_has_main_object as ro
            LEFT JOIN cat_registry as r using(registry_id)
            LEFT JOIN {$prefix}glossary AS g ON 
                g.handbk_id = " . \Handbks_Model::TYPE_OF_BUILDING . " 
                AND g.object_id = r.registry_id 
                AND g.`status` = " . MY_Model::STATUS_ACTIVE . "
            where ro.object_id = " . $this->id() . "
            and r.status = " . \MY_Model::STATUS_ACTIVE . "
            and r.handbk_id = " . \Handbks_Model::TYPE_OF_BUILDING  . ";";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Серии домов объекта
     * @return array
     */
    public function building_lot() {
        $prefix = $this->get_table_prefix();

        $sql = "SELECT ro.object_id, registry_id, r.name, r.handbk_id, g.alias
            FROM {$prefix}registry_has_main_object as ro
            LEFT JOIN cat_registry as r using(registry_id)
            LEFT JOIN {$prefix}glossary AS g ON 
                g.handbk_id = " . \Handbks_Model::BUILDING_LOT. " 
                AND g.object_id = r.registry_id 
                AND g.`status` = " . MY_Model::STATUS_ACTIVE . "
            where ro.object_id = " . $this->id() . "
            and r.status = " . \MY_Model::STATUS_ACTIVE . "
            and r.handbk_id = " . \Handbks_Model::BUILDING_LOT  . ";";
        
        return $this->db->query($sql)->result_array();
    }

    /**
     * Стоимость min & max
     * @return array
     */
    public function cost() {
        $sql = 'SELECT 
        (SELECT min(cost_min) FROM cat_flats WHERE cost_min != 0 AND object_id = ' . $this->id() . ') as cost_min,
        (SELECT max(cost_max) FROM cat_flats WHERE object_id = ' . $this->id() . ') as cost_max;';
        return $this->db->query($sql)->row_array();
    }

    /**
     * Метраж min & max
     * @return array
     */
    public function space() {
        $sql = 'SELECT 
        (SELECT min(space_min) FROM cat_flats WHERE space_min != 0 AND object_id = ' . $this->id() . ') as space_min,
        (SELECT max(space_max) FROM cat_flats WHERE object_id = ' . $this->id() . ') as space_max;';
        return $this->db->query($sql)->row_array();
    }

    /**
     * Планировки
     * @return array
     */
    public function pluns() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'plans');
        $file_category_id = element('file_category_id', $file_category);
        $pluns = $this->Image_Albums->get_with_images_by_object_id($this->id(), $file_category_id, TRUE);
        return $pluns;
    }

    /**
     * documents
     * @return array
     */
    private function documents() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'docs');
        $file_category_id = element('file_category_id', $file_category);

        $files = $this->Storage_Files->get_by_category($file_category_id, $this->id());
        $links = $this->Links->get_by_field('object_id', $this->id(), FALSE);
        // add ext for links
        foreach ($links as $key => $value)
            $links[$key]['ext'] = ($ext = pathinfo((string) element('link', $value), PATHINFO_EXTENSION)) ? $ext : 'doc';

        return array('files' => $files, 'links' => $links);
    }

    public function infrastructure_images() {
        return array();
    }

    /**
     * Получение списка объектов. 
     * Объекты содержат не все параметры, а только необходимые для выдачи списка. 
     * @param array/bool $ids - массив id объектов или будевое если нужны все объекты
     * @param int $limit - ограничение на страницу
     * @param int $offset - смещение
     * @param string $order_by - поле сортировки
     * @param string $order_direction - направление сортировки
     * @return array
     */
    public function get_short_list_by_ids($ids, $limit = false, $offset = 0, $order_by = 'cost', $order_direction = 'asc') {



        $prefix = $this->get_table_prefix();


        $file_category = $this->File_Categories->get_by_field('prefix', 'card');
        $order_direction = strtolower($order_direction) === 'asc' ? 'asc' : (strtolower($order_direction) === 'desc' ? 'desc' : 'asc');
//

//        var_dump('ddddddddddddd');die;
           switch ($order_by) {
               case 'cost':
               case 'cost_m':
               case 'space':
                   // order by flats
                   $order = $this->_order_by_flats_data($order_by, $order_direction);
                   break;
               case 'delivery':
                   // delivery date
                   $order = array('order_by' => 'delivery_year ' . $order_direction . ', delivery_quarter ' . $order_direction);
                   break;
               default :
                   $order = array('order_by' => $order_by . ' ' . $order_direction);
           }

         $conditions = " WHERE mo.status = 1";

              // если передан массив, то собираем по id
              if (is_array($ids))
                  $conditions .= " AND mo.id in (" . implode(',', $ids) . ")";



        if ($order['order_by'])
            $conditions .= " ORDER BY " . $order['order_by'];

        if ($limit !== FALSE)
            $conditions .= " LIMIT {$offset},{$limit}";


        /*
                $sql2 = "select SQL_CALC_FOUND_ROWS mo.id, mo.name, mo.adres, mo.adres as address, mo.alias,
                        (SELECT min(cost_min) FROM {$prefix}flats WHERE cost_min != 0 AND object_id = mo.id) as cost_min,
                        (SELECT min(space_min) FROM {$prefix}flats WHERE space_min != 0 AND object_id = mo.id) as space_min,
                        (SELECT max(space_max) FROM {$prefix}flats WHERE object_id = mo.id) as space_max,
                        (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f
                            left join {$prefix}file_involves as fi using(file_id)
                            left join {$prefix}file_formats as ff using(file_format_id)
                            left join {$prefix}file_types as ft using(file_type_id)
                            WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                        . " AND fi.parent_id = mo.id AND fi.sort = 1 LIMIT 0,1
                        ) as image_1,
                        (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f
                            left join {$prefix}file_involves as fi using(file_id)
                            left join {$prefix}file_formats as ff using(file_format_id)
                            left join {$prefix}file_types as ft using(file_type_id)
                            WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                        . " AND fi.parent_id = mo.id AND fi.sort = 2 LIMIT 0,1
                        ) as image_2,
                        (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f
                            left join {$prefix}file_involves as fi using(file_id)
                            left join {$prefix}file_formats as ff using(file_format_id)
                            left join {$prefix}file_types as ft using(file_type_id)
                            WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                        . " AND fi.parent_id = mo.id AND fi.sort = 3 LIMIT 0,1
                        ) as image_3,
                        d.quarter as delivery_quarter, d.year as delivery_year,
                        d.quarter_start as delivery_quarter_start, d.year_start as delivery_year_start,
                        m.x, m.y " . (isset($order['field']) ? ',' . $order['field'] : '') . "
                        FROM {$prefix}main_object as mo
                        left join {$prefix}meta as m on m.id_object = mo.id
                        left join {$prefix}delivery as d on d.object_id = mo.id
                        {$conditions};";




      $result = $this->db->query($sql)->result_array();

        var_dump($result);die;
*/
        $sql ="SELECT * FROM cat_flats WHERE cost_min != 0 AND object_id = 183";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    /**
     * Create order string by flats
     * @param string $order_by
     * @param string $order_direction
     * @return array
     */
    private function _order_by_flats_data($order_by, $order_direction) {
        $get = xss_clean($_GET);
        $rooms = element('rooms', $get, array());

        $direction = strtolower($order_direction) === 'asc' ? 'min' : 'max';

        $order_by .= '_' . $direction;

        // rooms 
        $room_condition = '';
        if (!empty($rooms)) {
            $room_condition .= ' AND room_id in (' . implode(',', $rooms) . ') ';
        }

        $order_field_string = '(SELECT ' . $direction . '(' . $order_by . ') '
                . ' FROM cat_flats WHERE object_id = mo.id ' . $room_condition . ' AND ' . $order_by . ' != 0) as order_' . $order_by;

        return array('field' => $order_field_string, 'order_by' => 'order_' . $order_by . ' ' . $order_direction);
    }

    /**
     * Get objects by name
     * @param string $name - substring of object name
     * @param int $status - status id
     * @return array
     */
    public function get_by_name($name, $status = 1) {
        $table = 'main_object as mo';
        $this->db->select('mo.id, mo.name, mo.status');
        $this->db->from($table);
        $this->db->like('mo.name', strval($name));

        if (is_numeric($status))
            $this->db->where('mo.status', $status);

        $result = $this->db->get()->result_array();
//        vdump($this->db->last_query());
        return $result;
    }

    /**
     * Get albums by object_id
     * @param int $object_id
     * @return array
     */
    public function get_albums($object_id = FALSE) {
        $file_category_id = 4;
        $object_id = !$object_id ? $this->id() : (int) $object_id;
        $result = $this->Image_Albums->get_with_images_by_object_id($object_id, $file_category_id, TRUE);
        return $result;
    }

    /**
     * Get objects by zone
     * @param string $zone - zone code
     * @return array
     */
    public function get_map_by_zone($zone) {
        if (is_array($zone)) {
            foreach ($zone as $key => $item) {
                if (!in_array($item, array_keys($this->Geo->zone)))
                    unset($zone[$key]);
                else
                    $zone[$key] = $this->Geo->zone[$item]->zone_id;
            }
        } else {
            $zone = element($zone, $this->Geo->zone, FALSE);
        }

        if (!$zone)
            return array();

        $prefix = $this->get_table_prefix();
        $file_category = $this->File_Categories->get_by_field('prefix', 'card');

        $this->db->select('mo.id, mo.name, mo.adres, mo.alias,'
                        . '(SELECT min(cost_min) FROM cat_flats WHERE cost_min != 0 AND object_id = mo.id) as cost_min,'
                        . '(SELECT min(space_min) FROM cat_flats WHERE space_min != 0 AND object_id = mo.id) as space_min,'
                        . '(SELECT max(space_max) FROM cat_flats WHERE object_id = mo.id) as space_max,'
                        . '(SELECT concat(f.name, ' . ', ff.ext) FROM ' . $prefix . 'storage_files as f 
                            left join ' . $prefix . 'file_involves as fi using(file_id)
                            left join ' . $prefix . 'file_formats as ff using(file_format_id)
                            left join ' . $prefix . 'file_types as ft using(file_type_id) 
                            WHERE file_category_id = ' . (int) element('file_category_id', $file_category, 0)
                        . ' AND fi.parent_id = mo.id AND fi.sort = 1 LIMIT 0,1
                        ) as image_1,'
                        . 'd.quarter as delivery_quarter, d.year as delivery_year,'
                        . 'd.quarter_start as delivery_quarter_start, d.year_start as delivery_year_start,'
                        . 'm.point as point,'
                        . (isset($order['field']) ? ',' . $order['field'] : '')
                        , FALSE)
                ->from('main_object as mo')
                ->join('meta as m', 'm.id_object = mo.id', 'left')
                ->join('delivery as d', 'd.object_id = mo.id', 'left');
        if (is_array($zone))
            $this->db->where_in('m.zone_id', $zone);
        else
            $this->db->where('m.zone_id', $zone->zone_id);

        $result = $this->db->where('mo.status', 1)->get()->result_array();

        return $result;
    }

    /**
     * Get object organizations
     * @param int $object_id
     * @param int $organization_type_id
     * @return array
     */
    public function get_organizations($object_id = FALSE, $organization_type_id = FALSE) {
        $object_id = !$object_id ? $this->id() : (int) $object_id;
        if ($organization_type_id)
            $this->db->where('organization_type_id', (int) $organization_type_id);

        return $this->db->where('object_id', (int) $object_id)
                        ->join('organizations', 'main_objects_organizations.organization_id = organizations.organization_id', 'left')
                        ->get('main_objects_organizations')->result_array();
    }

    /**
     * Set object organizations
     * @param int $object_id
     * @param array/int $organization_ids
     * @param int $organization_type_id
     * @return boolean
     */
    public function set_organizations($object_id, $organization_ids, $organization_type_id) {
        $object_id = !$object_id ? $this->id() : (int) $object_id;

        // check object exists
        if (!$this->db->where('id', $object_id)->get($this->table)->row_array())
            return FALSE;

        $this->load_model('Organizations_Model');

        $organization_type = $this->Organizations_Model->get_types(array('where' => array(array('organization_type_id' => (int) $organization_type_id))));
        if (!count($organization_type)) {
            log_message('error', 'Organization type not found (' . (int) $organization_type_id . ')');
            return FALSE;
        }
        // delete old organization relations
        $this->db->where('object_id', $object_id)->where('organization_type_id', (int) $organization_type_id)->delete('main_objects_organizations');
        // add organizations
        if (is_numeric($organization_ids)) {

            $dt = array(
                'object_id' => $object_id,
                'organization_id' => (int) $organization_ids,
                'organization_type_id' => (int) $organization_type_id,
            );

            if ($this->db->get_where('main_objects_organizations', $dt)->row_array())
                return FALSE;

            return !!$this->insert($dt, 'main_objects_organizations');
        } elseif (is_array($organization_ids)) {

            foreach ($organization_ids as $id) {
                if ((int) $id) {
                    $dt = array(
                        'object_id' => $object_id,
                        'organization_id' => (int) $id,
                        'organization_type_id' => (int) $organization_type_id,
                    );

                    if ($this->db->get_where('main_objects_organizations', $dt)->row_array())
                        continue;

                    $this->insert($dt, 'main_objects_organizations');
                }
            }
            return TRUE;
        } else {
            log_message('error', 'Incorrect argument $organization_ids - must be array or int');
            return FALSE;
        }
    }

    /**
     * Search objects 
     * @param array $filters:<br>
     * <b>organization_id</b> - int<br>
     * <b>zone_id</b> - int|array<br>
     * <b>district_id</b> - int|array<br>
     * <b>square_id</b> - int|array<br>
     * <b>geo_area_id</b> - int|array<br>
     * <b>populated_locality_id</b> - int|array<br>
     * <b>metro_station_id</b> - int|array<br>
     * <b>rooms</b> - int|array<br>
     * <b>cost_min</b> - int|array<br>
     * <b>cost_max</b> - int|array<br>
     * <b>complite</b> - array<br>
     * <b>status</b> - int|array<br>
     * <b>name_like</b> - int|string<br>
     * <b>tags</b> - string<br>
     * <b>order</b> - string [DEFAULT: order by id DESC]<br>
     * <b>order_direction</b> - string (ASC/DESC)<br>
     * <b>with</b> - array: ['created', 'updated', 'status']
     * @param bool $is_row
     * @return array
     */
    public function search(array $filters = array(), $is_row = FALSE) {
        $prefix = $this->get_table_prefix();
        $joins = $having = '';
        $conditions = $fields = array();
        $t = 'mo';
        $with = array_get($filters, 'with') && is_array($filters['with']) ? $filters['with'] : array();

        $limit = (int) element('limit', $filters, $this->limit);



        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";

        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;

        if (!!($order = array_get($filters, 'order'))) {
            // define order direction
            $order_direction = $this->order_direction(array_get($filters, 'order_direction'));

            // sort by cost (join flats)
            if ($order === 'cost') {
                $join_flats = TRUE;
                // ASC order direction - sort by cost_min
                // DESC order direction - sort by cost_max
                $order = 'ORDER BY f.cost_' . ($order_direction === 'ASC' ? 'min' : 'max') . ' ' . $order_direction;
            } elseif ($order === 'delivery') {
                // order by delivery
                $order = 'ORDER BY d.year , d.quarter ' . $order_direction;
            } else {
                $order = "ORDER BY " . $this->db->escape_str($order) . " " . $order_direction;

            }
        } else {
            $order = "ORDER BY {$t}.id DESC";
        }

        if (in_array('created', $with))
            $fields[] = "{$t}.created";
        if (in_array('updated', $with))
            $fields[] = "{$t}.updated";
        if (in_array('status', $with))
            $fields[] = "{$t}.status";
        // tags
        if (element('tags', $filters, FALSE)) {
            $tags = explode('|', $filters['tags']);
            $joins .= " left join {$prefix}main_object_tags using(object_id)";
            $joins .= " right join {$prefix}tags as t using(tag_id)";
            $search_type = !!isset($filters['search_type'][0]) ? $filters['search_type'][0] : 'and';
            $tmp_condition = '';
            if ($search_type === 'or') {
                foreach ($tags as $tag)
                    $tmp_condition .= ' OR t.name = ' . $this->escape($tag);
                $tmp_condition = ltrim($tmp_condition, ' OR');
            } else {
                $tmp_condition .= " t.name in ('" . implode("','", $tags) . "')";
                $tmp_condition = ltrim($tmp_condition, ' AND');

                $having = ' COUNT(DISTINCT tag_id) = ' . count($tags);
            }
            $conditions[] = "AND ({$tmp_condition})";
        }

        $file_category = $this->File_Categories->get_by_field('prefix', 'card');
        // conditions
        if (element('organization_id', $filters)) {
            $joins .= " LEFT JOIN {$prefix}main_objects_organizations AS mo_o ON mo_o.object_id = {$t}.id ";
            $conditions[] = "AND mo_o.organization_id = " . (int) $filters['organization_id'];
            $fields[] = 'organization_id';
            $fields[] = "(
                            SELECT GROUP_CONCAT(DISTINCT name separator ', ')
                            FROM {$prefix}main_objects_organizations
                            LEFT JOIN {$prefix}organization_types USING(organization_type_id)
                            WHERE organization_id = mo_o.organization_id and object_id = {$t}.id
                        ) AS organization_types";
        }

        if (array_get($filters, 'zone_id')) {
            if (is_array($filters['zone_id'])) {
                $conditions[] = 'AND m.zone_id IN(' . implode_int(',', $filters['zone_id']) . ')';
            } else {
                $conditions[] = 'AND m.zone_id = ' . (int) $filters['zone_id'];
            }
        }

        if (array_get($filters, 'district_id')) {
            if (is_array($filters['district_id'])) {
                $conditions[] = 'AND m.district_id IN(' . implode_int(',', $filters['district_id']) . ')';
            } else {
                $conditions[] = 'AND m.district_id = ' . (int) $filters['district_id'];
            }
        }
        
        if (array_get($filters, 'square_id')) {
            if (is_array($filters['square_id'])) {
                $conditions[] = 'AND m.square_id IN(' . implode_int(',', $filters['square_id']) . ')';
            } else {
                $conditions[] = 'AND m.square_id = ' . (int) $filters['square_id'];
            }
        }

        if (array_get($filters, 'geo_area_id')) {
            if (is_array($filters['geo_area_id'])) {
                $conditions[] = 'AND m.geo_area_id IN(' . implode_int(',', $filters['geo_area_id']) . ')';
            } else {
                $conditions[] = 'AND m.geo_area_id = ' . (int) $filters['geo_area_id'];
            }
        }

        if (array_get($filters, 'populated_locality_id')) {
            if (is_array($filters['populated_locality_id'])) {
                $conditions[] = 'AND m.populated_locality_id IN(' . implode_int(',', $filters['populated_locality_id']) . ')';
            } else {
                $conditions[] = 'AND m.populated_locality_id = ' . (int) $filters['populated_locality_id'];
            }
        }

        // cat_meta_metro
        if (array_get($filters, 'metro_station_id')) {
            $joins .= " LEFT JOIN {$prefix}meta_metro AS ms_o ON ms_o.object_id = {$t}.id ";
            if (is_array($filters['metro_station_id'])) {
                $conditions[] = 'AND ms_o.metro_id IN(' . implode_int(',', $filters['metro_station_id']) . ')';
            } else {
                $conditions[] = 'AND ms_o.metro_id = ' . (int) $filters['metro_station_id'];
            }
        }
        
        // status
        if (array_get($filters, 'status')) {
            if (is_array($filters['status'])) {
                $conditions[] = 'AND '. $t .'.status IN(' . implode_int(',', $filters['status']) . ')';
            } else {
                $conditions[] = 'AND '. $t .'.status = ' . (int) $filters['status'];
            }
        }
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND '. $t .'.id = ' . (int) $filters['name_like'];
            } elseif(is_string($filters['name_like'])) {
                $conditions[] = 'AND '. $t .'.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }

        // rooms
        if (array_get($filters, 'rooms')) {
            $join_flats = TRUE;
            if (is_array($filters['rooms'])) {
                // 4+ rooms
                if (in_array('4', $filters['rooms']))
                    $filters['rooms'][] = 5;

                $conditions[] = "AND f.room_id IN (" . implode_int(',', $filters['rooms']) . ")";
            } else {
                // 4+ rooms
                if (($room === (int) $filters['rooms']) === 4)
                    $conditions[] = "AND f.room_id IN(4,5)";
                else
                    $conditions[] = "AND f.room_id = " . $room;
            }
        }

        // define price type
        $price_type = (int) array_get($filters, 'price_type');

        // cost_min
        if (array_get($filters, 'cost_min')) {
            $join_flats = TRUE;
            $cost_min = (int) str_replace(' ', '', strval($filters['cost_min']));

//            var_dump($cost_min);die;
            // price_type = 1 => cost_m_min || price_type = 0 => cost_min
            !!$price_type ?
                            $conditions[] = 'AND (f.cost_m_max >= ' . $cost_min . ' OR (f.cost_m_min > 0 AND f.cost_m_max = 0))' :
                            $conditions[] = 'AND (f.cost_max >= ' . $cost_min . ' OR (f.cost_min > 0 AND f.cost_max = 0))';
        }

        // cost_max
        if (array_get($filters, 'cost_max')) {
            $join_flats = TRUE;
            $cost_max = (int) str_replace(' ', '', strval($filters['cost_max']));
            // price_type = 1 => cost_m_max || price_type = 0 => cost_max
            !!$price_type ? $conditions[] = 'AND f.cost_m_min <= ' . $cost_max : $conditions[] = 'AND f.cost_min <= ' . $cost_max;
        }

        // join flats
        if (isset($join_flats) && $join_flats)
            $joins .= " left join " . $this->db->dbprefix . "flats as f on f.object_id = {$t}.id";

        // delivery date
        if (is_array($complite = array_get($filters, 'complite')) && !!$complite) {

            $last_year = ($curr_year = date("Y")) + 2;

            if (($is_complite = array_search('1', $complite)) !== FALSE) {
                unset($complite[$is_complite]);
                $conditions[] = "AND (d.year = '" . $curr_year . "' OR (d.year < " . $curr_year . " AND d.year > 0))";
            }

            if ($complite) {
                if (in_array($last_year, $complite)) {
                    $conditions[] = "AND (d.year IN(" . implode_int(',', $complite) . ") OR d.year > " . $last_year . ")";
                } else {
                    $conditions[] = "AND d.year IN(" . implode_int(',', $complite) . ")";
                }
            }
        }

        $conditions = !!$conditions ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';
        $fields = !!$fields ? ' , ' . implode(', ', $fields) : '';
        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

/*

        $sql = "select SQL_CALC_FOUND_ROWS {$t}.id, {$t}.name, {$t}.adres, {$t}.alias, {$t}.adres as address,
                (SELECT min(cost_min) FROM {$prefix}flats WHERE cost_min != 0 AND object_id = {$t}.id) as cost_min,
                (SELECT min(space_min) FROM {$prefix}flats WHERE space_min != 0 AND object_id = {$t}.id) as space_min,
                (SELECT max(space_max) FROM {$prefix}flats WHERE object_id = {$t}.id) as space_max,
                (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f 
                    left join {$prefix}file_involves as fi using(file_id)
                    left join {$prefix}file_formats as ff using(file_format_id)
                    left join {$prefix}file_types as ft using(file_type_id) 
                    WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                . " AND fi.parent_id = {$t}.id AND fi.sort = 1 LIMIT 0,1
                ) as image_1,
                (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f 
                    left join {$prefix}file_involves as fi using(file_id)
                    left join {$prefix}file_formats as ff using(file_format_id)
                    left join {$prefix}file_types as ft using(file_type_id) 
                    WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                . " AND fi.parent_id = {$t}.id AND fi.sort = 2 LIMIT 0,1
                ) as image_2,
                (SELECT concat(f.name, '.', ff.ext) FROM {$prefix}storage_files as f 
                    left join {$prefix}file_involves as fi using(file_id)
                    left join {$prefix}file_formats as ff using(file_format_id)
                    left join {$prefix}file_types as ft using(file_type_id) 
                    WHERE file_category_id = " . (int) element('file_category_id', $file_category, 0)
                . " AND fi.parent_id = {$t}.id AND fi.sort = 3 LIMIT 0,1
                ) as image_3, 
                d.quarter as delivery_quarter, d.year as delivery_year,
                d.quarter_start as delivery_quarter_start, d.year_start as delivery_year_start,
                m.x, m.y {$fields}
                FROM {$prefix}main_object as {$t} 
                left join {$prefix}meta as m on m.id_object = {$t}.id
                left join {$prefix}delivery as d on d.object_id = {$t}.id
                {$joins} {$conditions} GROUP BY {$t}.id {$having} {$order} {$offset};";
*/
//        vdump($sql);

//        echo'<pre>';
//        var_dump(min($is_row));die;

        $sql = "SELECT * FROM cat_flats";
//        var_dump( $this->db->query($sql)->result_array());die;
        if ($is_row)
            return $this->db->query($sql)->row_array();

        return $this->db->query($sql)->result_array();
    }

    /**
     * 
     * @param array $object - object (Object_Model->search())
     * @param array $filters:<br>
     * <b>truncate_list</b> - array - list fro truncate: [(string)'field_1' => (int)50]. Add full_field1 - without truncate;<br>
     * @return type
     */
    public function prepare_object(array $object, array $filters = []) {

        // truncate_list
        $truncate_list = is_array(array_get($filters, 'truncate_list')) ? $filters['truncate_list'] : [];
        foreach ($truncate_list as $field => $length) {
            if (is_string(array_get($object, $field))) {
                $object['full_' . $field] = $object[$field];
                $object[$field] = truncate($object[$field], $length);
            }
        }
        // image
        if (array_get($filters, 'image'))
            $object['image'] = '/images/' . (array_get($object, $filters['image']) ? 'original/' . $object[$filters['image']] : 'no_photo.jpg');

        // cost
        $cost = is_array(array_get($filters, 'cost')) ? $filters['cost'] : [];
        foreach ($cost as $field) {
            $object[$field] = number_format(array_get($object, 'cost_min'), 0, '.', ' ');
        }

        if (array_get($filters, 'space')) {
            if (array_get($object, 'space_min'))
                $object['space_min'] = str_replace('.', ',', round($object['space_min'], 1));
            if (array_get($object, 'space_max'))
                $object['space_max'] = str_replace('.', ',', round($object['space_max'], 1));
        }

        // delivery
        if (array_get($filters, 'delivery')) {
            $dq = (int) array_get($object, 'delivery_quarter');
            $dqy = (int) array_get($object, 'delivery_year');
            $dqs = (int) array_get($object, 'delivery_quarter_start');
            $dqys = (int) array_get($object, 'delivery_year_start');

            if (($dq !== 0 && $dqy !== 0) && ($dqs !== 0 && $dqys !== 0))
                $object['delivery'] = 'с ' . $dqs . ' кв ' . $dqys . ' г. по ' . $dq . ' кв ' . $dqy . ' г.';
            elseif ($dq !== 0 && $dqy !== 0)
                $object['delivery'] = $dq . '-й квартал ' . $dqy . ' г.';
        }



        return $object;
    }

    /**
     * Set tags 
     * @param int $object_id
     * @param array $tags
     */
    public function set_tags($object_id, $tags) {
        $id = $object_id;
        // drop old tags
        $this->db->where('object_id', $id)->delete('main_object_tags');
        // set new tags
        foreach ($tags as $tag) {
            $this->db->insert('main_object_tags', array('object_id' => $id, 'tag_id' => (int) array_get($tag, 'tag_id')));
        }
    }

    /**
     * Get tags
     * @param int $object_id
     * @return array
     */
    public function get_tags($object_id, $only_name = TRUE) {
        $id = (int) $object_id;
        $prefix = $this->get_table_prefix();
        // fields
        $fileds = $only_name ? 't.name' : 't.name, t.tag_id, t.alias';

        $sql = "SELECT {$fileds} FROM {$prefix}main_object_tags as mt
                LEFT JOIN {$prefix}{$this->table} AS mo ON mo.id = mt.object_id
                LEFT JOIN {$prefix}tags AS t USING(tag_id)
                WHERE mt.object_id = {$id}";
        $tags = $this->db->query($sql)->result_array();

        if (!$only_name)
            return $tags;

        $result = array();
        foreach ($tags as $item) {
            if (isset($item['name']))
                $result[] = $item['name'];
        }
        return $result;
    }

    /**
     * get post (by tag relations)
     * @param int $object_id
     * @param array $filters
     * @return array
     */
    public function get_posts($object_id, array $filters = []) {

        $prefix = $this->get_table_prefix();

        $limit = (int) array_get($filters, 'limit', $this->limit);
        $offset = is_numeric(($offset = array_get($filters, 'offset'))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY p.created DESC";

        $sql = "SELECT fc.*, p.* FROM {$prefix}main_object_tags as ot
                left join {$prefix}posts_tags using(tag_id)
                left join {$prefix}posts as p using(post_id)
                left join {$prefix}file_categories as fc using(file_category_id)
                where p.status = 1 and fc.status = 1
                and fc.uri != '/' and ot.object_id = " . (int) $object_id . "
                group by post_id {$order} {$offset};";

        return $this->db->query($sql)->result_array();
    }

    /**
     * get infrastructure (registry)
     * @param int $object_id
     * @param array $filters: <br>
     * <b>with</b> - array: [parse_params - parse json to array field params]
     * @return array
     */
    public function infrastructure($object_id, array $filters = []) {

        $prefix = $this->get_table_prefix();
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];

        $sql = "SELECT rel.*, r.name, ri.category_id, rri.name as category_name, rri.params
                FROM {$prefix}registry_has_main_object  as rel
                left join {$prefix}registry as r using(registry_id)
                left join {$prefix}registry_infrastructure as ri using(registry_id)
                left join {$prefix}registry as rri on ri.category_id = rri.registry_id
                where object_id = " . (int) $object_id . "
                and ri.registry_id
                and r.status = " . self::STATUS_ACTIVE . ";";

        $result = $this->db->query($sql)->result_array();

        if (in_array('parse_params', $with)) {
            foreach ($result as $key => $item) {
                $result[$key]['params'] = json_decode($item['params'], TRUE);
            }
        }

        return $result;
    }

    /**
     * get status list
     * @return array
     */
    public function get_status_list() {
        return [
            self::STATUS_ACTIVE => ['alias' => 'active', 'title' => 'Опубликовано'],
            self::STATUS_NOT_PUBLISHED => ['alias' => 'not-published', 'title' => 'Черновик'],
            self::STATUS_ARCHIVE => ['alias' => 'archive', 'title' => 'Архив'],
        ];
    }

}
