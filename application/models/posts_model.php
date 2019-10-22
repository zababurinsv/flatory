<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * posts_model - news, articles, reviews
 *
 * @date 29.07.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Posts_Model extends MY_Model {

    /**
     * Types of posts
     * @var array 
     */
    private $_types = ['news', 'articles', 'reviews', 'geo_area', 'populated_locality', 'district', 'square', 'metro_station', 'macro_region'];
    
    /**
     * relations type - object relation
     * @var array 
     */
    private $_type_object_relations = [
        'geo_area' => ['table' => 'geo_area', 'status' => 1, 'primary_key' => 'geo_area_id', 'label' => 'name', 'order' => 'name'],
        'populated_locality' => ['table' => 'populated_locality', 'status' => 1, 'primary_key' => 'populated_locality_id', 'label' => 'name', 'order' => 'name'],
        'district' => ['table' => 'district', 'status' => 1, 'primary_key' => 'district_id', 'label' => 'name', 'order' => 'name'],
        'square' => ['table' => 'square', 'status' => 1, 'primary_key' => 'square_id', 'label' => 'name', 'order' => 'name'],
        'metro_station' => ['table' => 'metro_station', 'status' => 1, 'primary_key' => 'metro_station_id', 'label' => 'name', 'order' => 'name'],
        'macro_region' => ['table' => 'macro_regions', 'status' => 1, 'primary_key' => 'macro_region_id', 'label' => 'name', 'order' => 'name'],
    ];

    public function __construct() {
        parent::__construct();
        $this->table = 'posts';
        $this->primary_key = 'post_id';
    }

    /**
     * Get posts types
     * @return array
     */
    public function get_types() {
        return $this->_types;
    }

    /**
     * Search posts
     * @param array $filters - filters [status, file_category_id] :<br>
     * <b>name_like</b> - int|string<br>
     * <b>alias</b> - string<br>
     * <b>file_category_id</b> - int<br>
     * <b>status</b> - int|array<br>
     * @param bool $is_row - return as row
     * @return array
     */
    public function search(array $filters = [], $is_row = FALSE) {
        $prefix = $this->get_table_prefix();
        $joins = $having =  '';
        $conditions = $fields = [];
        
        $with = is_array(array_get($filters, 'with')) ? $filters['with'] : [];
        
        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY created DESC";

  
        $conditions[] = "AND p.status != " . self::STATUS_DELETED;
        
        // status
        if (array_get($filters, 'status')) {
            if (is_array($filters['status'])) {
                $conditions[] = 'AND p.status IN(' . implode_int(',', $filters['status']) . ')';
            } else {
                $conditions[] = 'AND p.status = ' . (int) $filters['status'];
            }
        }

        if (element('alias', $filters))
            $conditions[] = "AND p.alias = '" . xss_clean($filters['alias']) . "'";

        if (element('not_post_id', $filters))
            $conditions[] = "AND p.post_id != " . (int) $filters['not_post_id'];

        if (element('tag_id', $filters)) {
            $joins .= " LEFT JOIN {$prefix}posts_tags as pt USING(post_id)";
            if (is_array($filters['tag_id']))
                $conditions[] = "AND pt.tag_id IN(" . implode_int(',', $filters['tag_id']) . ")";
            else
                $conditions[] = "AND pt.tag_id = " . (int) $filters['tag_id'];
        }
        
        if(in_array('file_category_name', $with))
                $fields[] = 'cat_file_categories.name as file_category_name';
        
        if(in_array('file_category_alias', $with))
                $fields[] = 'cat_file_categories.prefix as file_category_alias';
        
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND p.post_id = ' . (int) $filters['name_like'];
            } elseif(is_string($filters['name_like'])) {
                $conditions[] = 'AND p.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }
        
        // search
        if (element('name', $filters, FALSE))
            $conditions[] = "AND p.name like '%" . trim($filters['name']) . "%' ";
        // data range
        if (element('date_begin', $filters, FALSE))
            $conditions[] = "AND DATE(p.created) >= " . $this->escape(date('Y-m-d', strtotime($filters['date_begin'])));
        if (element('date_end', $filters, FALSE))
            $conditions[] = "AND DATE(p.created) <= " . $this->escape(date('Y-m-d', strtotime($filters['date_end'])));
        
        // tags
        if (element('tags', $filters, FALSE)) {
            $tags = explode('|', $filters['tags']);
            $joins .= " left join {$prefix}posts_tags using(post_id)";
            $joins .= " right join {$prefix}tags as t using(tag_id)";
            $search_type = !!isset($filters['search_type'][0]) ? $filters['search_type'][0] : 'and';
            $tmp_condition = '';
            if ($search_type === 'or') {
                foreach ($tags as $tag)
                    $tmp_condition .= ' OR t.name = ' . $this->escape($tag);
                $tmp_condition = ltrim($tmp_condition, ' OR');
            } else {
                $tmp_condition .= " t.name in ('" . implode("','", $tags) . "')";
                $tmp_condition = ltrim($tmp_condition, ' AND');

                $having = ' COUNT(DISTINCT tag_id) = ' . count($tags);
            }
            $conditions[] = " AND ({$tmp_condition})";
        }


        if (is_numeric(element('file_category_id', $filters)))
            $conditions[] = "AND file_category_id = " . (int) $filters['file_category_id'];

        
        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';
        
        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;
        
        $fields = ltrim(implode(',', $fields), ',');
        
        $fields = !$fields ? '' : ',' .$fields;

        $sql = "SELECT SQL_CALC_FOUND_ROWS p.*, concat(ft.path, f.name, '.', ff.ext) as image {$fields}
                FROM {$prefix}posts as p
                LEFT JOIN {$prefix}file_categories USING(file_category_id)
                LEFT JOIN {$prefix}storage_files AS f USING(file_id)
                LEFT JOIN {$prefix}file_formats AS ff USING(file_format_id)
                LEFT JOIN {$prefix}file_types AS ft USING(file_type_id)
                {$joins} {$conditions} GROUP BY post_id {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

        if ($is_row)
            return $this->db->query($sql)->row_array();
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get post by id
     * @param int $id - post_id
     * @return array
     */
    public function get_post($id) {
        $prefix = $this->get_table_prefix();
        $sql = "SELECT p.*, concat(ft.path, f.name, '.', ff.ext) as image, fc.name as category_name, fc.prefix as category_alias
                FROM {$prefix}posts as p
                LEFT JOIN {$prefix}storage_files AS f USING(file_id)
                LEFT JOIN {$prefix}file_formats AS ff USING(file_format_id)
                LEFT JOIN {$prefix}file_types AS ft USING(file_type_id)
                LEFT JOIN {$prefix}file_categories as fc USING(file_category_id)
                WHERE post_id = " . (int) $id . ";";
        return $this->db->query($sql)->row_array();
    }

    /**
     * Get post tags
     * @param array/int $post
     * @return array
     */
    public function get_post_tags($post, $only_name = TRUE) {
        $id = is_array($post) ? (int) element('post_id', $post, 0) : (int) $post;
        $prefix = $this->get_table_prefix();
        // fields
        $fileds = $only_name ? 't.name' : 't.name, t.tag_id, t.alias';

        $sql = "SELECT {$fileds} FROM {$prefix}posts_tags 
                LEFT JOIN {$prefix}{$this->table} AS st USING({$this->primary_key})
                LEFT JOIN {$prefix}tags AS t USING(tag_id)
                WHERE {$this->primary_key} = {$id}";
        $tags = $this->db->query($sql)->result_array();

        if (!$only_name)
            return $tags;

        $result = array();
        foreach ($tags as $item) {
            if (isset($item['name']))
                $result[] = $item['name'];
        }
        return $result;
    }

    /**
     * Set post tags 
     * @param array/int $post
     * @param array $tags
     */
    public function set_post_tags($post, $tags) {
        $post_id = is_array($post) ? (int) element('post_id', $post, 0) : (int) $post;
        $prefix = $this->get_table_prefix();
        // drop old tags
        $this->db->where($this->primary_key, $post_id)->delete('posts_tags');
        // set new tags
        foreach ($tags as $tag) {
            $this->db->insert('posts_tags', array($this->primary_key => $post_id, 'tag_id' => (int) element('tag_id', $tag, 0)));
        }
    }

    /**
     * get type object releations
     * @return array
     */
    public function get_type_object_relations($type = NULL) {
        return $type ? array_get($this->_type_object_relations, $type, []) : $this->_type_object_relations;
    }
    
    /**
     * create alias
     * @param string $name
     * @return string
     */
    public function create_alias($name) {
        return transliteration($name);
    }
    
    /**
     * get status list
     * @return array
     */
    public function get_status_list() {
        return [
            self::STATUS_ACTIVE => ['alias' => 'active', 'title' => 'Опубликовано'],
            self::STATUS_NOT_PUBLISHED => ['alias' => 'not-published', 'title' => 'Черновик'],
            self::STATUS_ARCHIVE => ['alias' => 'archive', 'title' => 'Архив'],
        ];
    }
}
