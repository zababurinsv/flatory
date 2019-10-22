<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model tags
 *
 * @date 22.02.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Tags_Model extends MY_Model {

    protected $table = 'tags';
    protected $primary_key = 'tag_id';

    public function __construct() {
        parent::__construct();
    }

    /**
     * get tags (name)
     * @return array
     */
    public function get_tags() {
        $tags = $this->db->select('name')->from($this->table)->get()->result_array();
        $result = array();
        foreach ($tags as $item) {
            if (isset($item['name']))
                $result[] = $item['name'];
        }
        return $result;
    }

    /**
     * Update tags (add if not exists)
     * @param array $tags
     * @return array
     */
    public function update_tags($tags) {
        // case sensitive tags
        $tags_with_case = array();
        
        // change case tags to lower & save case sensitive tags
        foreach ($tags as $key => $item){
            $lower_case_tag = mb_strtolower($item);
            $tags_with_case[$lower_case_tag] = $item;
            $tags[$key] = $lower_case_tag;
        }
            
        // get tags without case sensitive
        $tag_exists = $this->db->select($this->primary_key . ', name')->from($this->table)->where_in('name', $tags)->get()->result_array();
        
        // change case on exists tags to lower
        foreach ($tag_exists as $key => $item)
            $tag_exists[$key]['name'] = mb_strtolower($item['name']);
        
        // create tags tree 
        $tree_tag_exists = !empty($tag_exists) ? simple_tree($tag_exists, 'name') : $tag_exists;
        
        // check new tags
        $new_tags = array();
        foreach ($tags as $tag) {
            if (!isset($tree_tag_exists[$tag]) && $tag)
                $new_tags[] = array('name' => $tmp = $tags_with_case[$tag], 'alias' => transliteration(mb_strtolower($tmp)));
        }
        foreach ($new_tags as $key => $item) {
            // check alias
            if ($this->get_by_field('alias', $item['alias']))
                $item['alias'] = $item['alias'] . '-' . time();

            $new_tags[$key][$this->primary_key] = $this->insert($item);
        }
        $tags = array_merge($tag_exists, $new_tags);
        return $tags;
    }

    /**
     * Update by primary key
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update_by_primary_key($id, $data) {
        if (!isset($data['name']))
            return FALSE;
        $data['name'] = $data['name'];
        $data['alias'] = transliteration($data['name']);
        return parent::update_by_primary_key($id, $data);
    }

    /**
     * Delete permanently by id
     * @param int $tag_id
     */
    public function delete_permanently($tag_id) {
        $this->db->delete($this->table, array($this->primary_key => (int)$tag_id));
    }
    
    /**
     * Search
     * @param array $filters : <br>
     * <b>tag_id</b> - int/array <br>
     * <b>name_like</b> - int <br>
     * @param bool $is_row
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $t = 't';
        $prefix = $this->get_table_prefix();
        $joins = $having = $conditions = $fields = $group_by = [];
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $found_rows = in_array('found_rows', $with) ? 'SQL_CALC_FOUND_ROWS' : '';
    
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
