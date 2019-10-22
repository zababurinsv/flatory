<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * metro_line
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Metro_Line_Model extends MY_Model {
    
    public function __construct() {
        parent::__construct();
        
        $this->table = 'metro_line';
        $this->primary_key = 'metro_line_id';
    }
    
    /**
     * Search
     * @param array $filters : <br>
     * <b>metro_line_id</b> - int/array <br>
     * <b>name_like</b> - int <br>
     * <b>status</b> - int <br>
     * @param bool $is_row
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $t = 't';
        $prefix = $this->get_table_prefix();
        $joins = $having = $conditions = $fields = $group_by = [];
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $found_rows = in_array('found_rows', $with) ? 'SQL_CALC_FOUND_ROWS' : '';

        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;
        
        if (array_get($filters, $this->primary_key))
            if (is_array($filters[$this->primary_key]))
                $conditions[] = "AND " . $this->primary_key . " IN (" . implode_int(',', $filters[$this->primary_key]) . ")";
            else
                $conditions[] = "AND " . $this->primary_key . " = " . (int) $filters[$this->primary_key];
    
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.'. $this->primary_key .' = ' . (int) $filters['name_like'];
            } elseif (is_string($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }
        
        if (array_get($filters, 'status'))
            if (is_array($filters['status']))
                $conditions[] = "AND {$t}.status IN (" . implode_int(',', $filters['status']) . ")";
            else
                $conditions[] = "AND {$t}.status = " . (int) $filters['status'];

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
}
