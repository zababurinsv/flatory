<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of geo_area_model
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Geo_Area_Model extends MY_Model {

    /**
     * model File_Categories
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        $this->table = 'geo_area';
        $this->primary_key = 'geo_area_id';
    }

    public function search(array $filters = [], $is_row = FALSE) {
        $t = 'ga';
        $prefix = $this->get_table_prefix();
        $joins = $having = $conditions = $fields = $group_by = [];
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $found_rows = in_array('found_rows', $with) ? 'SQL_CALC_FOUND_ROWS' : '';
        
        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;

        if (in_array('posts', $with)) {
            $this->load_model('File_Categories');
            $file_category_id = (int) array_get($this->File_Categories->get_by_field('prefix', $this->table), 'file_category_id');
            $joins[] = "LEFT JOIN {$prefix}posts AS p ON {$t}.{$this->primary_key} = p.object_id AND p.file_category_id = " . $file_category_id;
            $fields = array_merge($fields, ['post_id', 'alias', 'p.name AS post_name', 'content', 'anons']);
        }
        // with geo_direction
        if(in_array('geo_direction', $with)){
            $joins[] = "LEFT JOIN {$prefix}geo_direction as dr ON {$t}.geo_direction_id = dr.geo_direction_id";
            $fields = array_merge($fields, ['dr.name as geo_direction']);
        }
        // @todo array
        if (array_get($filters, $this->primary_key))
            $conditions[] = "AND " . $this->primary_key . " = " . (int) $filters[$this->primary_key];

        // @todo array
        if (array_get($filters, 'zone_id'))
            $conditions[] = "AND zone_id = " . (int) $filters['zone_id'];
        // @todo array
        if (array_get($filters, 'geo_direction_id'))
            $conditions[] = "AND {$t}.geo_direction_id = " . (int) $filters['geo_direction_id'];

        if (is_bool(array_get($filters, 'is_count_objects'))) {
            if ($filters['is_count_objects'])
                $conditions[] = "AND count_objects";
            else
                $conditions[] = "AND !count_objects";
        }

        if (array_get($filters, 'status'))
            if (is_array($filters['status']))
                $conditions[] = "AND {$t}.status IN (" . implode_int(',', $filters['status']) . ")";
            else
                $conditions[] = "AND {$t}.status = " . (int) $filters['status'];
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.'. $this->primary_key .' = ' . (int) $filters['name_like'];
            } elseif (is_string($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }

        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "";

        $conditions = !!$conditions ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';
        $having = !!$having ? 'HAVING ' . ltrim(implode(' ', $having), 'AND') : '';
        $group_by = !!$group_by ? 'GROUP BY ' . ltrim(implode(' ', $group_by), 'AND') : '';
        $joins = !!$joins ? implode(' ', $joins) : '';

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

        $fields = ltrim(implode(',', $fields), ',');

        $fields = !$fields ? '' : ',' . $fields;

//        $sql = "SELECT {$found_rows} {$t}.* {$fields} FROM {$prefix}{$this->table} AS {$t} {$joins} {$conditions} {$group_by} {$having} {$order} {$offset};";
        $sql = "SELECT *  FROM {$prefix}{$this->table}";

//        vdump($sql, 1);
//        vdump($filters, 1);
//        var_dump('geo_area_model');
//        var_dump($sql);die;
        if ($is_row)
            return $this->db->query($sql)->row_array();
        return $this->db->query($sql)->result_array();
    }

    /**
     * get data map
     * @return array
     */
    public function get_data_map() {
        return [
            'label' => 'name',
            'value' => $this->primary_key,
            'alias' => 'alias',
            'count_objects' => 'count_objects',
        ];
    }

}
