<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Object_Section_Model
 *
 * @author Valery
 */
class Object_Section_Model extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->table = 'object_sections';
        $this->primary_key = 'object_section_id';
    }

    /**
     * Search
     * @param array $filters - filters [status, file_category_id]
     * @param bool $is_row - return as row
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $prefix = $this->get_table_prefix();
        $joins = $conditions = $fields = [];

        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];

        $limit = (int) array_get($filters, 'limit', $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = !!($order = array_get($filters, 'order')) ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(array_get($filters, 'order_direction', 'ASC'))}" : "ORDER BY created DESC";

        $conditions[] = "AND os.status != " . self::STATUS_DELETED;
        
        if (array_get($filters, 'is_has_object')) {
            $fields[] = "COALESCE((select object_id
                    from {$prefix}object_sections_has_main_object 
                    where object_section_id = os.object_section_id and object_id = " . (int) $filters['is_has_object'] . "), is_default) as is_has_object";
        }

        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';
        $joins = implode(' ', $joins);
        $fields = ltrim(implode(',', $fields), ',');
        $fields = !$fields ? '' : ',' . $fields;

        $sql = "SELECT SQL_CALC_FOUND_ROWS os.* {$fields} FROM {$prefix}object_sections as os {$joins} {$conditions} {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

        return $is_row ? $this->db->query($sql)->row_array() : $this->db->query($sql)->result_array();
    }

    /**
     * Set object sections
     * @param int $object_id - id 
     * @param array $section_ids - list of id
     */
    public function set_object_sections($object_id, array $section_ids) {
        // rm old records
        $this->db->where('object_id', (int) $object_id)->delete('object_sections_has_main_object');
       
        // set new records
        foreach ($section_ids as $section_id) {
            $this->db->insert('object_sections_has_main_object', [
                $this->primary_key => (int) $section_id,
                'object_id' => (int) $object_id,
                'created' => date('Y-m-d H:i:s'),
            ]);
            
        }
    }

}
