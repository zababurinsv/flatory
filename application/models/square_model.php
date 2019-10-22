<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of square_model
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Square_Model extends MY_Model {

    /**
     * model File_Categories
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        $this->table = 'square';
        $this->primary_key = 'square_id';
    }

    /**
     * 
     * @param array $filters
     * @param type $is_row
     * @return type
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $t = 's';
        $prefix = $this->get_table_prefix();
        $joins = $having = $conditions = $fields = $group_by = [];
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $found_rows = in_array('found_rows', $with) ? 'SQL_CALC_FOUND_ROWS' : '';
        
        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;

        if (in_array('posts', $with)) {
            $this->load_model('File_Categories');
            $file_category_id = (int) array_get($this->File_Categories->get_by_field('prefix', $this->table), 'file_category_id');
            $joins[] = "LEFT JOIN {$prefix}posts AS p ON {$t}.{$this->primary_key} = p.object_id AND p.file_category_id = " . $file_category_id;
            $fields = array_merge($fields, ['p.post_id', 'p.alias', 'p.name AS post_name', 'p.content', 'p.anons']);

            // parent_alias
            if (in_array('post_parent_alias', $with)) {
                $joins[] = "LEFT JOIN {$prefix}posts AS pp ON s.district_id = pp.object_id AND pp.file_category_id = (select file_category_id from {$prefix}file_categories where prefix = 'district')";
                $fields = array_merge($fields, ['pp.alias as parent_alias']);

                if (array_get($filters, 'post_parent_alias'))
                    $conditions[] = "AND pp.alias = '" . $this->db->escape_str($filters['post_parent_alias']) . "'";
            }
        }
        
        // with district
        if(in_array('district', $with)){
            $joins[] = "LEFT JOIN {$prefix}district as d USING(district_id)";
            $fields = array_merge($fields, ['d.name as district']);
        }

        if (array_get($filters, $this->primary_key))
            if (is_array($filters[$this->primary_key]))
                $conditions[] = "AND " . $this->primary_key . " IN (" . implode_int(',', $filters[$this->primary_key]) . ")";
            else
                $conditions[] = "AND " . $this->primary_key . " = " . (int) $filters[$this->primary_key];

        if (array_get($filters, 'district_id'))
            if (is_array($filters['district_id']))
                $conditions[] = "AND district_id IN (" . implode_int(',', $filters['district_id']) . ")";
            else
                $conditions[] = "AND district_id = " . (int) $filters['district_id'];

        if (is_bool(array_get($filters, 'is_count_objects'))) {
            if ($filters['is_count_objects'])
                $conditions[] = "AND {$t}.count_objects";
            else
                $conditions[] = "AND !{$t}.count_objects";
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
            'value' => 'square_id',
            'parent_id' => 'district_id',
            'alias' => 'alias',
            'parent_alias' => 'parent_alias',
            'count_objects' => 'count_objects',
        ];
    }

}
