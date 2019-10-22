<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Storage files model
 *
 * @date 21.02.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Storage_Files extends MY_Model {

    public $limit = 10;

    /**
     * Default sort
     * @var int
     */
    private $defaul_sort_index = 99;

    const FILE_IMAGE = 1;
    const FILE_DOC = 2;

    public function __construct() {
        parent::__construct();

        $this->table = 'storage_files';
        $this->primary_key = 'file_id';

        $this->load->model('File_Types');
        $this->load->model('Tags_Model');
        $this->load->model('Proportions');

        $this->load->library('image_lib');
    }

    /**
     * Get default sort index
     * @return int
     */
    public function get_defaul_sort_index() {
        return $this->defaul_sort_index;
    }

    /**
     * Список пропорций
     * @param int $status
     * @param int $offset
     * @param string $order
     * @param string $order_direction
     * @param int $limit
     * @return array
     */
    public function get_proportions($status = 1, $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;
        $table = 'proportions';
        $this->db->ar_found_rows = TRUE;
        $this->db->select('*');
        $this->db->from($table);

        if (is_numeric($status))
            $this->db->where('status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = $this->db->get()->result_array();
        return $result;
    }

    /**
     * Получить формат файла 
     * @param string $type - атрибут файла type 
     * @return array
     */
    public function get_file_format($type) {
        $type = explode('/', $type);
        $ext = element(1, $type, '');
        $type = $this->db->select('*')->from('file_formats')->where('name', $ext)->get()->row_array();
        return $type;
    }

    /**
     * Get with type
     * @todo rewrite on AR
     * @param string $value
     * @param string $field
     * @return array 
     */
    public function get_with_type($value, $field = NULL, $all_files = FALSE) {
        $field = !$field ? $this->primary_key : $field;
        $prefix = $this->get_table_prefix();
        $condition = is_array($value) && $field === $this->primary_key ? "f." . $field . " IN (" . implode(',', $value) . ")" : "f." . $field . " = '" . $value . "';";
        $sql = "SELECT f.*, concat(f.name, '.', ff.ext) as file_name, ff.ext, ff.file_type_id, ft.name as file_type, ft.path, ft.alias as file_type_alias
                FROM " . $prefix . $this->table . " as f
                left join " . $prefix . "file_formats as ff using(file_format_id)
                left join " . $prefix . "file_types as ft using(file_type_id) 
                WHERE " . $condition . ";";
        if ((is_array($value) && $field === $this->primary_key) || $all_files)
            return $this->db->query($sql)->result_array();

        return $this->db->query($sql)->row_array();
    }

    /**
     * Get by primary key
     * @param int $file_id
     * @return array
     */
    public function get_by_primary_key($file_id) {
        return $this->get_with_type($file_id);
    }

    /**
     * Get by primary key
     * @param int $file_id
     * @return array
     */
    public function get_by_primary_keys($file_ids) {
        return $this->get_with_type($file_ids);
    }

    /**
     * Get by file types
     * @param string $value
     * @param string $field
     * @param type $offset
     * @param type $order
     * @param type $order_direction
     * @param type $limit
     * @return array
     */
    public function get_by_file_type($value, $field = NULL, $filter = array(), $offset = FALSE, $order = false, $order_direction = 'ASC', $limit = FALSE) {

        $field = !$field ? $this->File_Types->get_primary_key() : $field;
        $prefix = $this->get_table_prefix();

        $limit = !$limit ? $this->limit : $limit;
        $offset = $offset !== FALSE && $offset !== -1 ? "LIMIT {$offset}, {$limit}" : "";
        $order = $order !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction($order_direction)}" : "ORDER BY created DESC";

        $conditions = $joins = $having = $fields = '';

        // with_categories
        if (element('with_categories', $filter, FALSE) || is_numeric(array_get($filter, 'is_category')))
            $fields .= ",(SELECT GROUP_CONCAT(DISTINCT name) FROM {$prefix}file_involves
                        LEFT JOIN {$prefix}file_categories USING(file_category_id)
                        WHERE file_id = f.file_id) AS categories";

        // with_tags
        if (element('with_tags', $filter, FALSE) || is_numeric(array_get($filter, 'is_tags')))
            $fields .= ",(SELECT GROUP_CONCAT(name) FROM {$prefix}files_tags
                        LEFT JOIN {$prefix}tags USING(tag_id)
                        WHERE file_id = f.file_id) AS tags";

        // with_total_size
        if (element('with_total_size', $filter, FALSE))
            $fields .= ",(COALESCE((SELECT SUM(size) FROM {$prefix}files_proportions WHERE file_id = f.file_id),0) + f.size) AS total_size";

        if (element('with_watermarks', $filter, FALSE) || is_numeric(array_get($filter, 'is_watermark')))
            $fields .= ",(SELECT (CASE WHEN (COUNT(file_id) > 0) THEN 1 ELSE 0 END)
                 FROM {$prefix}files_proportions WHERE is_watermark AND file_id = f.file_id) AS is_watermark";

        // search
        if (element('name', $filter, FALSE))
            $conditions .= " AND f.original_name like '%" . trim($filter['name']) . "%' ";
        // data range
        if (element('date_begin', $filter, FALSE))
            $conditions .= " AND DATE(f.created) >= " . $this->escape(date('Y-m-d', strtotime($filter['date_begin'])));
        if (element('date_end', $filter, FALSE))
            $conditions .= " AND DATE(f.created) <= " . $this->escape(date('Y-m-d', strtotime($filter['date_end'])));
        // tags
        if (element('tags', $filter, FALSE)) {
            $tags = explode('|', $filter['tags']);
            $joins .= " left join {$prefix}files_tags using(file_id)";
            $joins .= " right join {$prefix}tags as t using(tag_id)";
            $search_type = !!isset($filter['search_type'][0]) ? $filter['search_type'][0] : 'and';
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
            $conditions .= " AND ({$tmp_condition})";
        }
        // file_category_id
        if (element('file_category_id', $filter, FALSE)) {
            $joins .= " left join {$prefix}file_involves using(file_id)";
            $conditions .= " AND file_category_id = " . (int) $filter['file_category_id'];
        }
        // parent_id
        if (element('parent_id', $filter, FALSE)) {
            if (strpos($joins, 'file_involves') === FALSE)
                $joins .= " left join {$prefix}file_involves using(file_id)";
            $conditions .= " AND parent_id = " . (int) $filter['parent_id'];
        }
        // file_id
        if (element('file_id', $filter, FALSE))
            $conditions .= " AND file_id = " . (int) $filter['file_id'];

        // proportion_id
        if (element('proportion_id', $filter, FALSE)) {
            $joins .= " left join {$prefix}files_proportions using(file_id)";

            if ((int) $filter['proportion_id'] === -1)
                $conditions .= " AND proportion_id is null";
            else
                $conditions .= " AND proportion_id = " . (int) $filter['proportion_id'];
        }
        // is_square
        if (is_numeric(element('is_square', $filter, FALSE))) {
            if ((int) $filter['is_square'] === 1) {
                $conditions .= " AND x = y";
            } else {
                $conditions .= " AND x != y";
            }
        }

        if (array_get($filter, 'file_format_id') && is_array($filter['file_format_id']))
            $conditions .= " AND file_format_id IN (" . implode_int(',', $filter['file_format_id']) . ")";

        // image_width filter
        if (is_numeric(element('image_width', $filter)) && in_array(($statement = element('image_width_eq', $filter)), array('>', '>=', '=', '<=', '<'))) {
            $conditions .= " AND x {$statement} " . (int) $filter['image_width'];
        }

        if (is_numeric(element('is_description', $filter, FALSE))) {
            $statement = (int) $filter['is_description'] ? '!=' : '=';
            $conditions .= " AND TRIM(COALESCE(f.description, '')) {$statement} ''";
        }

        if (is_numeric(element('is_alt', $filter, FALSE))) {
            $statement = (int) $filter['is_alt'] ? '!=' : '=';
            $conditions .= " AND TRIM(COALESCE(f.alt, '')) {$statement} ''";
        }

        if (is_numeric(element('is_watermark', $filter, FALSE)))
            $having .= " AND is_watermark = " . (int) $filter['is_watermark'];

        if (is_numeric(element('is_tags', $filter, FALSE))) {
            $statement = (int) $filter['is_tags'] ? '!=' : '=';
            $having .= " AND TRIM(COALESCE(tags, '')) {$statement} ''";
        }

        if (is_numeric(element('is_category', $filter, FALSE))) {
            $statement = (int) $filter['is_category'] ? '!=' : '=';
            $having .= " AND TRIM(COALESCE(categories, '')) {$statement} ''";
        }

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;


        /*
         *         $sql = "SELECT SQL_CALC_FOUND_ROWS
                f.*, concat(f.name, '.', ff.ext) as file_name, ff.ext,
                ff.file_type_id, ft.name as file_type, ft.path {$fields}, ft.alias as file_type_alias
                FROM {$prefix}{$this->table} as f
                left join {$prefix}file_formats as ff using(file_format_id)
                left join {$prefix}file_types as ft using(file_type_id) {$joins}
                WHERE ft.{$field} = '{$value}' {$conditions} GROUP BY f.file_id {$having} {$order} {$offset};";
         */

        $sql = "SELECT * FROM {$prefix}{$this->table}";



//        var_dump('ffff');die;
//        vdump($sql, 1);
//        vdump($filter, 1);
        return $this->db->query($sql)->result_array();;
    }

    /**
     * Check is image file
     * @param array $file - $this->get_with_type()
     * @return bool
     */
    public function is_image($file) {
        return (int) element('file_type_id', $file, 0) === self::FILE_IMAGE ? TRUE : FALSE;
    }

    /**
     * Get file proportions
     * @param array/int $file - file or file_id
     * @return array
     */
    public function get_file_proportions($file) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $prefix = $this->get_table_prefix();
        $sql = "SELECT * FROM " . $prefix . "files_proportions
                LEFT JOIN " . $prefix . "proportions using(proportion_id)
                WHERE status = 1
                AND file_id = " . $file_id . ";";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Add image proportion
     * @param array/int $file
     * @param array/int $proportion
     * @param int $size
     * @param int $is_watermark
     */
    public function add_file_proportion($file, $proportion, $size, $is_watermark) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $proportion_id = is_array($proportion) ? (int) element('proportion_id', $proportion, 0) : (int) $proportion;

        $primary = array($this->primary_key => $file_id, 'proportion_id' => $proportion_id);
        $isset_file_proportion = $this->db->from('files_proportions')->where($primary)->get()->row_array();

        if (empty($isset_file_proportion)) {
            $new = array(
                'file_id' => $file_id,
                'proportion_id' => $proportion_id,
                'size' => (int) $size,
                'is_watermark' => (int) $is_watermark,
            );
            $this->db->insert('files_proportions', $new);
        } else {
            $this->db->where($primary)->update('files_proportions', array(
                'size' => (int) $size,
                'is_watermark' => (int) $is_watermark,
            ));
        }
        $this->file_was_updated($file);
    }

    /**
     * Delete file proportion
     * @param array/int $file
     * @param array/int $proportion
     */
    public function delete_file_proportion($file, $proportion) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $proportion_id = is_array($proportion) ? (int) element('proportion_id', $proportion, 0) : (int) $proportion;
        $primary = array($this->primary_key => $file_id, 'proportion_id' => $proportion_id);

        $this->db->delete('files_proportions', $primary);
        $this->file_was_updated($file);
    }

    /**
     * Get total file size
     * @param array/int $file
     * @return int
     */
    public function get_total_size($file) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $prefix = $this->get_table_prefix();
        $sql = "select size + ifnull((select sum(size) from {$prefix}files_proportions where file_id = {$file_id}), 0) as total_size
                from {$prefix}storage_files 
                where file_id = {$file_id};";
        $result = $this->db->query($sql)->row_array();
        return element('total_size', $result, 0);
    }

    /**
     * Get file involves
     * @param type $file
     * @return array 
     */
    public function get_file_involves($file) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $prefix = $this->get_table_prefix();
        $sql = "SELECT * FROM cat_file_involves 
                left join {$prefix}file_categories using(file_category_id)
                WHERE file_id = {$file_id};";

        $result = $this->db->query($sql)->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $item) {
                $result[$key]['uri'] = str_replace('{id}', $item['parent_id'], str_replace('{alias}', $item['parent_alias'], $item['uri']));
                $result[$key]['uri_adm'] = str_replace('{id}', $item['parent_id'], str_replace('{alias}', $item['parent_alias'], $item['uri_adm']));
            }
        }
        return $result;
    }

    /**
     * Get file tags
     * @param array/int $file
     * @return array
     */
    public function get_file_tags($file, $only_name = TRUE) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $prefix = $this->get_table_prefix();
        // fields
        $fileds = $only_name ? 't.name' : 't.name, t.tag_id';

        $sql = "SELECT {$fileds} FROM {$prefix}files_tags 
                LEFT JOIN {$prefix}{$this->table} AS st USING({$this->primary_key})
                LEFT JOIN {$prefix}tags AS t USING(tag_id)
                WHERE {$this->primary_key} = {$file_id}";
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
     * Set file tags 
     * @param array/int $file
     * @param array $tags
     */
    public function set_file_tags($file, $tags) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $prefix = $this->get_table_prefix();
        // drop old tags
        $this->db->where($this->primary_key, $file_id)->delete('files_tags');
        // set new tags
        foreach ($tags as $tag) {
            $this->db->insert('files_tags', array($this->primary_key => $file_id, 'tag_id' => (int) element('tag_id', $tag, 0)));
        }
    }

    /**
     * File was updated
     * @param array/int $file
     */
    public function file_was_updated($file) {
        $file_id = is_array($file) ? (int) element('file_id', $file, 0) : (int) $file;
        $this->update_by_primary_key($file_id, array('updated' => date('Y-m-d H:i:s')));
    }

    /**
     * update files
     * @param array $file_ids
     * @param array $data
     * 
     * @todo one foreach $file_ids
     * @todo set errors in $result
     * @todo refactoring
     * 
     * @return array
     */
    public function update_files($file_ids, $data) {
        // will return data about changes
        $result = array(
            'changed_files' => 0,
            'changed_file_proportions' => 0,
            'errors' => array(),
            'files' => $this->get_by_primary_keys($file_ids),
        );
        $edit = array();
        $tags = array();
        if (element_strict('alt', $data, FALSE) !== FALSE)
            $edit['alt'] = $data['alt'];

        if (element_strict('description', $data, FALSE) !== FALSE)
            $edit['description'] = $data['description'];

        // update tags
        if (element_strict('tags', $data, FALSE) !== FALSE) {
            $tags = is_string($data['tags']) ? explode('|', $data['tags']) : $data['tags'];
            $tags = $this->Tags_Model->update_tags($tags);
        }
        // update tags
        if (!empty($tags)) {
            foreach ($file_ids as $file_id) {
                $this->set_file_tags($file_id, $tags);
            }
            $edit['updated'] = date('Y-m-d H:i:s');
        }

        // add tags
        if (element_strict('tags_add', $data, FALSE) !== FALSE) {
            $tags_add = is_string($data['tags_add']) ? explode('|', $data['tags_add']) : $data['tags_add'];
            $tags_add = $this->Tags_Model->update_tags($tags_add);
            $tags_add = !empty($tags_add) ? simple_tree($tags_add, 'tag_id') : $tags_add;

            foreach ($file_ids as $file_id) {
                $file_tags = $this->get_file_tags($file_id, FALSE);
                $file_tags = !empty($file_tags) ? simple_tree($file_tags, 'tag_id') : $file_tags;

                $new_file_tags = array_except($tags_add, array_keys($file_tags));
                foreach ($new_file_tags as $nt) {
                    $this->db->insert('files_tags', array($this->primary_key => $file_id, 'tag_id' => (int) element('tag_id', $nt, 0)));
                }
            }
        }

        // proportions
        $proportions_add = element('proportions', $data, array());
        if (!empty($proportions_add)) {
            // get meta data about proportions
            $proportions_add = simple_tree($proportions_add, 'proportion_id');
            $proportions_meta = $this->Proportions->get_by_primary_keys(array_keys($proportions_add));
            $proportions_meta = !empty($proportions_meta) ? simple_tree($proportions_meta, 'proportion_id') : $proportions_meta;

            foreach ($file_ids as $file_id) {
                // check is image
                $file = $this->get_by_primary_key($file_id);
                if (!$this->is_image($file))
                    continue;

                // add only new or changed proportions
                $file_proportions = $this->get_file_proportions($file_id);
                $file_proportions = !empty($file_proportions) ? simple_tree($file_proportions, 'proportion_id') : $file_proportions;

                $new_file_proportions = array();
                foreach ($proportions_add as $add) {
                    // check is new proportion for file
                    if (!element_strict($add['proportion_id'], $file_proportions, FALSE)) {
                        $new_file_proportions[] = $add;
                    } else {
                        $current_file_proportions = $file_proportions[$add['proportion_id']];
                        // check is chanched proportions (watermark)
                        if ((int) $current_file_proportions['is_watermark'] !== (int) element_strict('is_watermark', $add))
                            $new_file_proportions[] = $add;
                    }
                }

                // add count in result
                $result['changed_file_proportions'] += count($new_file_proportions);

                // add new proportions
                foreach ($new_file_proportions as $new_proportion) {
                    $meta_proportion = element($new_proportion['proportion_id'], $proportions_meta, array());

                    // @todo error: proportion not found
                    if (empty($meta_proportion))
                        continue;

                    $width = element('x', $meta_proportion, 0);
                    $heigth = element('y', $meta_proportion, 0);

                    // resize
                    $is_resize = $this->image_lib->resize($file['file_name'], $width, $heigth);

                    if ($is_resize) {
                        $edit['updated'] = date('Y-m-d H:i:s');
                        // update relation file_proportions
                        $file_path = DOCROOT . 'images' . DIRECTORY_SEPARATOR
                                . $width . $this->image_lib->dest_folder_size_separator . $heigth
                                . DIRECTORY_SEPARATOR . $file['file_name'];
                        $size = filesize($file_path);
                        $is_watermark = (int) element('is_watermark', $new_proportion, 0);

                        $this->Storage_Files->add_file_proportion($file, $meta_proportion, $size, $is_watermark);

                        // add watermark
                        if ($is_watermark)
                            $is_watermark = $this->image_lib->watermark($file_path);
                    }
                }
            }
        }

        if (!empty($edit)) {
            $this->db->where_in($this->primary_key, $file_ids)->update($this->table, $edit);
            $result['changed_files'] = count($file_ids);
        }
        return $result;
    }

    /**
     * Get files by category & parent_id
     * @param int $category_id - file_category_id
     * @param int $parent_id - parent_id (file_category)
     * @param bool $is_first - return only first row
     * @return array
     */
    public function get_by_category($category_id, $parent_id, $is_first = FALSE) {
        $prefix = $this->get_table_prefix();
        $sql = "select f.*, concat(f.name, '.', ff.ext) as file_name, ff.ext, ff.file_type_id, ft.name as file_type, ft.path, fi.sort
                from {$prefix}storage_files as f
                left join {$prefix}file_involves as fi using(file_id)
                left join {$prefix}file_formats as ff using(file_format_id)
                left join {$prefix}file_types as ft using(file_type_id) 
                where file_category_id = " . (int) $category_id . " and parent_id = " . (int) $parent_id . " 
                order by sort;";
        if (!$is_first)
            return $this->db->query($sql)->result_array();

        return $this->db->query($sql)->row_array();
    }

    /**
     * Set files involves
     * @param int $category_id
     * @param int $parent_id
     * @param string $alias
     * @param array $files
     * @param array $sorts
     */
    public function set_files_involves($category_id, $parent_id, $alias, $files, $sorts = array()) {
        // delete old files for current category & parent
        $this->db->delete('file_involves', array('file_category_id' => (int) $category_id, 'parent_id' => (int) $parent_id));
        foreach ($files as $file_id) {
            $this->db->insert('file_involves', array(
                'file_category_id' => (int) $category_id,
                'file_id' => (int) $file_id,
                'parent_id' => (int) $parent_id,
                'parent_alias' => $alias,
                'sort' => element($file_id, $sorts, $this->defaul_sort_index),
            ));
        }
    }

    public function delete_marked_for_deletion() {
        $files = $this->get_with_type(self::STATUS_DELETED, 'status', TRUE);
        foreach ($files as $file) {
            $this->_delete_by_primary_key($file);
        }
    }

    /**
     * Delete file with all relation & proportions
     * !!! DROPPING ALL RELATIONS !!!
     * @param array/int $file - $this->get_by_primary_key
     * @return boolean
     */
    private function _delete_by_primary_key($file) {
        $file = !is_array($file) ? $this->get_by_primary_key((int) $file) : $file;
        if (!isset($file['file_id']) || !isset($file['path']))
            return FALSE;

        // delete file involves
        $this->db->delete('file_involves', array($this->primary_key => $file['file_id']));

        if ($this->is_image($file)) {
            // only for images
            // delete thumb
            unlink($this->image_lib->thumb_folder . $file['file_name']);
            // delete relations:
            // proportions
            $this->db->delete('files_proportions', array($this->primary_key => $file['file_id']));
            $proportions = $this->Proportions->get_list();
            $glue = $this->image_lib->dest_folder_size_separator;
            // delete all proportions files
            foreach ($proportions as $prop) {
                unlink(DOCROOT . 'images/' . $prop['x'] . $glue . $prop['y'] . '/' . $file['file_name']);
            }
            // albums
            $this->db->delete('files_image_albums', array($this->primary_key => $file['file_id']));
        }

        // delete original file
        unlink(DOCROOT . $file['path'] . $file['file_name']);
        // delete from current table
        parent::delete_by_primary_key($file['file_id']);

        return TRUE;
    }

    /**
     * Delete file involve (category)
     * @param int $file_id
     * @param int $category_id
     * @param int $parent_id
     * @return bool
     */
    public function delete_file_involve($file_id, $category_id, $parent_id) {
        return $this->db->delete('file_involves', array('file_category_id' => (int) $category_id, 'parent_id' => (int) $parent_id, 'file_id' => (int) $file_id));
    }

    public function add_file_involve($file_id, $category_id, $parent_id, $alias) {
        $this->db->insert('file_involves', array(
            'file_category_id' => (int) $category_id,
            'file_id' => (int) $file_id,
            'parent_id' => (int) $parent_id,
            'parent_alias' => $alias,
        ));
    }

    /**
     * Delete file & all relations and proportions
     * @param array/int $file
     * @return bool
     */
    public function delete($file) {
        return $this->_delete_by_primary_key($file);
    }

}
