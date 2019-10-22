<?php

/**
 * Model of proportions
 *
 * @date 15.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Proportions extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->table = 'proportions';
        $this->primary_key = 'proportion_id';
    }

    /**
     * Get category proportions
     * @param int $category_id category id
     * @return array
     */
    public function get_category_proportions($category_id) {        
        $prefix = $this->get_table_prefix();
        $sql = "SELECT * FROM {$prefix}file_categories_proportions
                LEFT JOIN {$prefix}proportions USING({$this->primary_key}) 
                WHERE file_category_id = " . (int) $category_id . ";";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Create proportion (with dir)
     * @todo check proportion exists
     * @param array $data
     */
    public function create($data) {
        $proportion_id = $this->insert($data);
        if ($proportion_id && isset($data['name']))
            mkdir(DOCROOT . 'images/' . $data['name']);

        return $proportion_id;
    }

    /**
     * Set category proportions
     * @param int $category_id
     * @param int $proportion_id
     * @param bool $is_watermark
     */
    public function set_category_proportions($category_id, $proportion_id, $is_watermark = FALSE) {
        $relation = $this->db->where('file_category_id', (int) $category_id)
                ->where($this->primary_key, (int) $proportion_id)
                ->get('file_categories_proportions')
                ->result_array();
        if (!count($relation))
            $this->insert(array(
                'file_category_id' => (int) $category_id,
                $this->primary_key => (int) $proportion_id,
                'is_watermark' => (bool) $is_watermark,
                    ), 'file_categories_proportions');
    }

}
