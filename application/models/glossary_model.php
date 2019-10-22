<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Glossary Model
 *
 * @date 03.09.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Glossary_Model extends MY_Model {

    /**
     * model Image_Albums
     * @var \Image_Albums 
     */
    public $Image_Albums;

    /**
     * model File_Categories
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();
        $this->table = 'glossary';
        $this->primary_key = 'glossary_id';
    }

    /**
     * Search glossary
     * @param array $filters - filters : <br> [status, parent_id]
     * <b>status</b> - int<br>
     * <b>parent_id/array</b> - int<br>
     * <b>alias</b> - string<br>
     * <b>only_parents</b> - bool - получить только родителей<br>
     * <b>with</b> - array : <br>
     * - <b>handbk</b> - array - list of handbks.{fields}
     * @param bool $is_row - return as row
     * @return array
     */
    public function search($filters, $is_row = FALSE) {

        $t = 'g';

        $prefix = $this->get_table_prefix();
        $joins = $having = '';
        $conditions = array();

        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        $fields = [];

        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY created DESC";

        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;

        // with handbk
        if (array_get($with, 'handbks')) {
            $joins .= "LEFT JOIN {$prefix}handbks USING(handbk_id)";
            $fields = is_array($with['handbks']) ? array_merge($fields, $with['handbks']) : $fields[] = $with['handbks'];
        }

        // only_parents
        if (array_get($filters, 'only_parents'))
            $conditions[] = "AND {$t}.glossary_id IN (select parent_id from {$prefix}glossary where parent_id group by parent_id)";

        // status
        if (array_get($filters, 'status')) {
            if (is_array($filters['status'])) {
                $conditions[] = "AND {$t}.status IN(" . implode_int(',', $filters['status']) . ")";
            } else {
                $conditions[] = "AND {$t}.status = " . (int) $filters['status'];
            }
        }

        if (element('alias', $filters))
            $conditions[] = "AND {$t}.alias = '" . xss_clean($filters['alias']) . "'";

        if (is_numeric(element_strict('parent_id', $filters)))
            $conditions[] = "AND {$t}.parent_id = " . (int) $filters['parent_id'];

        if (is_array(element_strict('parent_id', $filters)))
            $conditions[] = "AND {$t}.parent_id IN (" . implode_int(',', $filters['parent_id']) . ")";

        if (element('not_id', $filters))
            $conditions[] = "AND {$t}.glossary_id != '" . (int) $filters['not_id'] . "'";

        // search
        if (element('name', $filters, FALSE))
            $conditions[] = "AND {$t}.name like '%" . trim($filters['name']) . "%' ";
        // data range
        if (element('date_begin', $filters, FALSE))
            $conditions[] = "AND DATE({$t}.created) >= " . $this->escape(date('Y-m-d', strtotime($filters['date_begin'])));
        if (element('date_end', $filters, FALSE))
            $conditions[] = "AND DATE({$t}.created) <= " . $this->escape(date('Y-m-d', strtotime($filters['date_end'])));

        if (is_numeric(element_strict('handbk_related', $filters))) {
            if ($filters['handbk_related'])
                $conditions[] = "AND (object_id AND handbk_id)";
            else
                $conditions[] = "AND (!object_id AND (!handbk_id OR handbk_id IS NULL))";
        }

        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.' . $this->primary_key . ' = ' . (int) $filters['name_like'];
            } elseif (is_string($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }

        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

        $fields = !!$fields ? ',' . implode(',', $fields) : '';

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$t}.*,
                (SELECT name FROM {$prefix}glossary WHERE glossary_id = {$t}.parent_id) as parent_name {$fields}
                FROM {$prefix}glossary as {$t}
                {$joins} {$conditions} GROUP BY glossary_id {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

        if ($is_row)
            return $this->db->query($sql)->row_array();
        return $this->db->query($sql)->result_array();
    }

    /**
     * Delete glossary item
     * @param int $glossary_id - id
     */
    public function delete_by_primary_key($glossary_id) {
        // load models
        $this->load_model('File_Categories');
        $this->load_model('Image_Albums');
        // delete related alums
        $category = $this->File_Categories->get_by_field('prefix', $this->table);
        if (!!($file_category_id = element('file_category_id', $category))) {
            $albums = $this->Image_Albums->get_by_object_id($glossary_id, $file_category_id);
            foreach ($albums as $it) {
                $this->Image_Albums->delete_by_primary_key($it[$this->Image_Albums->get_primary_key()]);
            }
        }
        // delete item
        parent::delete_by_primary_key($glossary_id);
    }

    /**
     * get by related handbk objects
     * @param int $handbk_id
     * @param int/array $object_ids
     * @return array
     */
    public function get_by_related_objects($handbk_id, $object_ids) {
        $this->db->where('handbk_id', (int) $handbk_id);
        if (is_array($object_ids))
            $this->db->where_in('object_id', $object_ids);
        else
            $this->db->where('object_id', $object_ids);

        return $this->db->get($this->table)->result_array();
    }

}
