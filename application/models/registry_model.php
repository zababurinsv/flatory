<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * registry_model
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Registry_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'registry';
        $this->primary_key = 'registry_id';
    }

    /**
     * Search
     * @param array $filters : <br>
     * <b>registry_id</b> - int <br>
     * <b>handbk_id</b> - int <br>
     * <b>object_id</b> - int <br>
     * <b>name</b> - string  (like)<br>
     * <b>handbks_groups_alias</b> - string <br>
     * <b>status</b> - int Registry_Model::STATUS_* <br>
     * <b>with</b> - array [handbk_name, objects_relations] <br>
     * <b>limit</b> - int <br>
     * <b>offset</b> - int <br>
     * <b>order</b> - string <br>
     * <b>order_direction</b> - string <br>
     * @param bool  $is_row - return first row [DEFAULT: TRUE]
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $prefix = $this->get_table_prefix();
        $joins = $having = '';
        $conditions = $fields = [];
        $t = 'r';

        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];

        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY name ASC";

        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;
        
        // handbk_name
        if (in_array('handbk_name', $with) || array_get($filters, 'handbks_groups_alias')) {
            $joins .= 'LEFT JOIN ' . $prefix . 'handbks as h USING(handbk_id) ';
            $fields[] = 'h.name as handbk_name';
            $fields[] = 'h.alias as handbk_alias';

            // handbks_groups_alias
            if (array_get($filters, 'handbks_groups_alias')) {
                $joins .= 'left join ' . $prefix . 'handbks_groups as hg using(handbks_group_id)';
                $conditions[] = "AND hg.alias = '" . $filters['handbks_groups_alias'] . "'";
            }
        }

        // objects_relations
        if (in_array('objects_relations', $with)) {
            $fields[] = "(select count(object_id) from {$prefix}registry_has_main_object as rel where rel.registry_id = {$t}.registry_id ) as objects_relations";
        }

        // registry_id
        if (array_get($filters, 'registry_id'))
            $conditions[] = "AND {$t}.`registry_id` = " . (int) $filters['registry_id'];

        // handbk_id
        if (array_get($filters, 'handbk_id'))
            $conditions[] = "AND `handbk_id` = " . (int) $filters['handbk_id'];

        // object_id
        if (array_get($filters, 'object_id')) {
            $joins .= 'LEFT JOIN ' . $prefix . 'registry_has_main_object as rel USING(registry_id) ';
            $conditions[] = 'AND rel.object_id = ' . (int) $filters['object_id'];
        }

        // status
        if (array_get($filters, 'status')) {
            if (is_array($filters['status'])) {
                $conditions[] = 'AND '. $t .'.status IN(' . implode_int(',', $filters['status']) . ')';
            } else {
                $conditions[] = 'AND '. $t .'.status = ' . (int) $filters['status'];
            }
        }

        // name
        if (array_get($filters, 'name'))
            $conditions[] = "AND {$t}.name like '%" . trim($filters['name']) . "%' ";
            
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.'. $this->primary_key .' = ' . (int) $filters['name_like'];
            } elseif (is_string($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }

        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

        $fields = ltrim(implode(',', $fields), ',');

        $fields = !$fields ? '' : ',' . $fields;

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$t}.* {$fields} FROM {$prefix}registry as {$t} {$joins} {$conditions} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);
        // get result
        $result = $is_row ? $this->db->query($sql)->row_array() : $this->db->query($sql)->result_array();

        if (in_array('parse_params', $with)) {

            if ($is_row) {

                if (array_get($result, 'params'))
                    $result['params'] = json_decode($result['params'], TRUE);
            } else {

                foreach ($result as $key => $val) {
                    $result[$key]['params'] = json_decode($val['params'], TRUE);
                }
            }
        }

        return $result;
    }

    private function _prepare_registry(array $registry) {
        if (array_get($registry, 'params'))
            $registry['params'] = json_encode($registry['params']);
        return $registry;
    }

    /**
     * Insert registry
     * @param array $data
     * @param string $table
     * @return int - inserted id
     */
    public function insert($data, $table = FALSE) {

        if (!$table) {
            $data = $this->_prepare_registry($data);

            if (!array_get($data, 'created'))
                $data['created'] = date('Y-m-d H:i:s');
        }

        return parent::insert($data, $table);
    }

    public function update_by_primary_key($id, $data) {
        $data = $this->_prepare_registry($data);
        return parent::update_by_primary_key($id, $data);
    }

    /**
     * Delete by primary key
     * @param int $id
     */
    public function delete_by_primary_key($id) {
        $this->db->where('registry_id', (int) $id)->delete('registry_has_main_object');
        $this->db->where($this->primary_key, (int) $id)->update($this->table, ['status' => self::STATUS_DELETED]);
    }

    /**
     * Prepare registry handbks
     * @param array $registry - list of registry with handbk_name
     * @return array (tree)
     */
    public function prepare_registry_handbks(array $registry) {
        $registry_handbks = [];

        foreach ($registry as $item) {
            $handbk_id = array_get($item, 'handbk_alias', array_get($item, 'handbk_id'));
            if (!isset($registry_handbks[$handbk_id])) {
                $registry_handbks[$handbk_id] = ['name' => array_get($item, 'handbk_name'), 'alias' => array_get($item, 'handbk_alias'), 'list' => []];
            }
            $registry_handbks[$handbk_id]['list'][] = $item;
        }

        return $registry_handbks;
    }

    /**
     * Delete from registry_has_main_object
     * @param string $handbks_groups_alias - alias
     * @param int $object_id - id of object
     */
    public function delete_object_relations_by_handbks_groups_alias($handbks_groups_alias, $object_id) {
        $sql = "delete rel from cat_registry_has_main_object as rel
                left join cat_registry using(registry_id)
                left join cat_handbks using(handbk_id)
                left join cat_handbks_groups as hg using(handbks_group_id)
                where hg.alias = '{$handbks_groups_alias}' and rel.object_id = " . (int) $object_id . ";";


//        vdump($sql, 1);
        $this->db->query($sql);
    }

    /**
     * Delete from registry_has_main_object by handbk_ids & object_id
     * @param array/int $handbk_ids
     * @param int $object_id
     */
    public function delete_object_relations_by_handbks($handbk_ids, $object_id) {

        $condition = is_array($handbk_ids) && !!$handbk_ids ? "handbk_id IN (" . implode_int(',', $handbk_ids) . ")" : "handbk_id =" . (int) $handbk_ids;

        $sql = "delete rel from cat_registry_has_main_object as rel
                left join cat_registry using(registry_id)
                where {$condition} and rel.object_id = " . (int) $object_id . ";";

        $this->db->query($sql);
    }

}
