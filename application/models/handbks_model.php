<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Handbks Model
 */
class Handbks_Model extends MY_Model {
    
    // type_of_building
    const TYPE_OF_BUILDING = 1;
    // building_lot
    const BUILDING_LOT = 2;
    
    public function __construct() {
        parent::__construct();
        $this->table = 'handbks';
        $this->primary_key = 'handbk_id';
    }

    /**
     * get related handbks
     * @param array $filters - filters
     * @return array 
     */
    public function get_related_handbk($filters = array()) {
        if (is_numeric(element($this->primary_key, $filters))) {
            $handbk = $this->get_by_primary_key($handbk_id = (int) $filters[$this->primary_key]);
            if (!element('table', $handbk))
                return array();

            $where = is_array(array_get($filters, 'where')) ? $filters['where'] : [];
            
            if($handbk['table'] === 'registry')
                $where[] = ['handbk_id' => $handbk_id];
            
            return $this->get_all(array_merge($filters, array('table' => $handbk['table'], 'where' => $where, 'order' => 'name')));
        }
    }

    /**
     * Search
     * @param array $filters : <br>
     * <p><b>handbk_id</b> - int </p>
     * <p><b>table</b> - string </p>
     * <p><b>with</b> - array : ['handbks_groups']</p>
     * @param bool $is_row - return only first row [default: false]
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $prefix = $this->get_table_prefix();
        $joins = $having = '';
        $conditions = $fields = [];
        $t = 'h';

        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];

        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY name ASC";

        if (array_get($filters, 'table'))
            $conditions[] = "AND `table` = '" . $this->db->escape_like_str($filters['table']) . "'";

        if (array_get($filters, 'handbk_id'))
            $conditions[] = "AND `handbk_id` = '" . (int) $filters['handbk_id'] . "'";

        // handbks_groups
        if (in_array('handbks_groups', $with)) {
            $joins .= 'left join ' . $prefix . 'handbks_groups as hg using(handbks_group_id)';
            $fields = array_merge($fields, ['hg.name as group_name', 'hg.alias as group_alias']);
        }

        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

        $fields = ltrim(implode(',', $fields), ',');

        $fields = !$fields ? '' : ',' . $fields;

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$t}.* {$fields} FROM {$prefix}handbks as {$t} {$joins} {$conditions} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);
        // get result
        $result = $is_row ? $this->db->query($sql)->row_array() : $this->db->query($sql)->result_array();

        if (in_array('parse_params', $with)) {

            if ($is_row) {

                if (array_get($result, 'params')) {
                    $result['params'] = json_decode($result['params'], TRUE);
                    // get_params_data
                    if (in_array('get_params_data', $with)) {
                        $result = $this->_parse_data_params($result);
                    }
                }
            } else {
                // @todo
            }
        }

        return $result;
    }

    private function _parse_data_params(array $handbk) {

        if (!($fields = array_get($handbk, 'params.fields')))
            return $handbk;

        foreach ($fields as $key => $item) {
            if (!($data_scheme = array_get($item, 'data_scheme')))
                continue;
            
            $this->load_model($model = $data_scheme['model']);
            $args = array_get($data_scheme, 'args', []);
            $data = call_user_func_array([$this->$model, $data_scheme['method']], $args);
            
            $tmp_data = [];
            foreach ($data as $it){
                $tmp_data[array_get($it, $data_scheme['value'])] = array_get($it, $data_scheme['title']);
            }
            
            $handbk['params']['fields'][$key]['data'] = $tmp_data;            
        }

        return $handbk;
    }

    /**
     * Get infrastructure list
     * @param array $filters
     * @return array
     */
    public function get_infrastructure_list(array $filters = []) {
        
        $sql = 'SELECT r.*, cat.name as category_name 
                FROM cat_registry_infrastructure as ri
                left join cat_registry as r using(registry_id)
                left join cat_registry as cat on ri.category_id = cat.registry_id
                where r.status = ' . self::STATUS_ACTIVE . ';';
        
        // @todo filters
        
        return $this->db->query($sql)->result_array();
    }
}
