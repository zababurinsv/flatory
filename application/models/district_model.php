<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of district_model
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class District_Model extends MY_Model {

    /**
     * model File_Categories
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        $this->table = 'district';
        $this->primary_key = 'district_id';
    }

    public function search(array $filters = [], $is_row = FALSE) {
        $t = 'd';
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

        if (is_bool(array_get($filters, 'is_count_objects'))) {
            if ($filters['is_count_objects'])
                $conditions[] = "AND count_objects";
            else
                $conditions[] = "AND !count_objects";
        }

        $int_conditions = [$this->primary_key, 'populated_locality_id'];

        foreach ($int_conditions as $f) {
            if (array_get($filters, $f))
                if (is_array($filters[$f]))
                    $conditions[] = "AND " . $f . " IN (" . implode_int(',', $filters[$f]) . ")";
                else
                    $conditions[] = "AND " . $f . " = " . (int) $filters[$f];
        }


        if (array_get($filters, 'status'))
            if (is_array($filters['status']))
                $conditions[] = "AND {$t}.status IN (" . implode_int(',', $filters['status']) . ")";
            else
                $conditions[] = "AND {$t}.status = " . (int) $filters['status'];

        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.' . $this->primary_key . ' = ' . (int) $filters['name_like'];
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

        $test = $prefix.$this->table;

        $sql = "SELECT * FROM cat_district ";
//        $sql = "SELECT {$found_rows} {$t}.* {$fields} FROM {$prefix}{$this->table} AS {$t} {$joins} {$conditions} {$group_by} {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

//        $result = $this->db->query($sql)->row_array();
//        var_dump($result);
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
