<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of metro_station
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Metro_Station_Model extends MY_Model {

    /**
     * model File_Categories
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        $this->table = 'metro_station';
        $this->primary_key = 'metro_station_id';
    }

    /**
     * Search
     * @param array $filters : <br>
     * <b>metro_station_id</b> - int/array <br>
     * <b>metro_line_id</b> - int <br>
     * <b>name_like</b> - int <br>
     * <b>status</b> - int <br>
     * <b>with</b> - array : ['posts']<br>
     * @param bool $is_row
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $t = 's';
        $prefix = $this->get_table_prefix();
        $joins = $having = $conditions = $fields = $group_by = [];
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $found_rows = in_array('found_rows', $with) ? 'SQL_CALC_FOUND_ROWS' : '';

        if (in_array('posts', $with)) {
            $this->load_model('File_Categories');
            $file_category_id = (int) array_get($this->File_Categories->get_by_field('prefix', $this->table), 'file_category_id');
            $joins[] = "LEFT JOIN {$prefix}posts AS p ON {$t}.{$this->primary_key} = p.object_id AND p.file_category_id = " . $file_category_id;
            $fields = array_merge($fields, ['post_id', 'alias', 'p.name AS post_name', 'content', 'anons']);
        }
        
        if (array_get($filters, $this->primary_key))
            if (is_array($filters[$this->primary_key]))
                $conditions[] = "AND " . $this->primary_key . " IN (" . implode_int(',', $filters[$this->primary_key]) . ")";
            else
                $conditions[] = "AND " . $this->primary_key . " = " . (int) $filters[$this->primary_key];

        // @todo array
        if (array_get($filters, 'metro_line_id'))
            $conditions[] = "AND metro_line_id = " . (int) $filters['metro_line_id'];

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

        $sql = "SELECT {$found_rows} {$t}.* {$fields} FROM {$prefix}{$this->table} AS {$t} {$joins} {$conditions} {$group_by} {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

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

    /**
     * get station lines
     * @param int/array $metro_station_id 
     * @return array
     */
    public function get_station_lines($metro_station_id) {
        
        if(is_array($metro_station_id))
            $this->db->where_in($this->primary_key, $metro_station_id);
        else
            $this->db->where($this->primary_key, (int) $metro_station_id);
        
        return $this->db->join('metro_line', 'metro_line.metro_line_id = metro_station_metro_line.metro_line_id')->get('metro_station_metro_line')->result_array();
    }

    /**
     * Update metro station by primary key
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update_by_primary_key($id, $data) {

        if (!($lines = array_get($data, 'metro_line_id')) || !is_array($lines)) {
            return parent::update_by_primary_key($id, $data);
        }

        $this->db->trans_start();
        // delete old lines
        $this->db->delete('metro_station_metro_line', [$this->primary_key => $id]);

        foreach ($lines as $line_id)
            $this->db->query("INSERT INTO " . $this->get_table_prefix() . "metro_station_metro_line (" . $this->primary_key . ", metro_line_id) VALUES(" . (int) $id . ", " . (int) $line_id . ") ON DUPLICATE KEY UPDATE " . $this->primary_key . " = " . (int) $id . ", metro_line_id = " . (int) $line_id . ";");

        unset($data['metro_line_id']);

        if ($res = parent::update_by_primary_key($id, $data))
            $this->db->trans_complete();

        return $res;
    }

    /**
     * Insert metro station statement
     * @param array $data
     * @param string $table
     * @return int - inserted id
     */
    public function insert($data, $table = FALSE) {

        if (($lines = array_get($data, 'metro_line_id')) || is_array($lines))
            unset($data['metro_line_id']);

        $id = parent::insert($data, $table);

        if ($lines && $id)
            $this->db->trans_start();

        foreach ($lines as $line_id)
            $this->db->query("INSERT INTO " . $this->get_table_prefix() . "metro_station_metro_line (" . $this->primary_key . ", metro_line_id) VALUES(" . (int) $id . ", " . (int) $line_id . ") ON DUPLICATE KEY UPDATE " . $this->primary_key . " = " . (int) $id . ", metro_line_id = " . (int) $line_id . ";");

        $this->db->trans_complete();

        return $id;
    }

}
