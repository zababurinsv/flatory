<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model image_albums
 *
 * @date 22.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Image_Albums extends MY_Model {

    /**
     * Default sort
     * @var int
     */
    private $defaul_sort_index = 99;
    
    public function __construct() {
        parent::__construct();

        $this->table = 'image_albums';
        $this->primary_key = 'image_album_id';
    }

    /**
     * Get default sort index
     * @return int
     */
    public function get_defaul_sort_index() {
        return $this->defaul_sort_index;
    }

    /**
     * get active albums bu object_id
     * @param int $object_id - object_id
     * @param inr $file_category_id - file_category_id
     * @return array
     */
    public function get_by_object_id($object_id, $file_category_id = FALSE) {
        if ($file_category_id !== FALSE)
            $this->db->where('file_category_id', (int) $file_category_id);
        return $this->db->where('object_id', (int) $object_id)
                        ->order_by('sort')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get albums with images by object_id
     * @param int $object_id
     * @return array
     */
    public function get_with_images_by_object_id($object_id, $file_category_id = FALSE, $return_tree = FALSE) {
        $condition = $file_category_id !== FALSE ? ' AND ia.file_category_id = ' . (int) $file_category_id : '';
        $prefix = $this->get_table_prefix();
        $sql = "SELECT ia.image_album_id, object_id, ia.name as album_name, ia.description as album_description, ia.file_category_id, ia.sort as album_sort,
                    f.*, concat(f.name, '.', ff.ext) as file_name, ff.ext, ff.file_type_id, ft.name as file_type, ft.path, fia.sort
                FROM " . $prefix . $this->table . " as ia
                left join " . $prefix . "files_image_albums as fia using(" . $this->primary_key . ") 
                left join " . $prefix . "storage_files as f using(file_id)
                left join " . $prefix . "file_formats as ff using(file_format_id)
                left join " . $prefix . "file_types as ft using(file_type_id) 
                WHERE object_id = " . (int) $object_id . $condition . " AND file_id IS NOT NULL
                ORDER BY album_sort, fia.sort;";
                
        $result = $this->db->query($sql)->result_array();

        if ($return_tree) {
            $tree = array();
            foreach ($result as $arr) {
                if (!isset($tree[$arr[$this->primary_key]]))
                    $tree[$arr[$this->primary_key]] = array(
                        $this->primary_key => $arr[$this->primary_key],
                        'album_name' => $arr['album_name'],
                        'album_description' => $arr['album_description'],
                        'file_category_id' => $arr['file_category_id'],
                        'object_id' => $arr['object_id'],
                    );
                $tree[$arr[$this->primary_key]]['images'][] = $arr;
            }
            return $tree;
        }

        return $result;
    }

    public function get_with_images_by_id($album_id, $return_tree = FALSE) {
        $prefix = $this->get_table_prefix();
        $sql = "SELECT ia.image_album_id, object_id, ia.name as album_name, ia.description as album_description, ia.file_category_id, ia.sort as album_sort,
                    f.*, concat(f.name, '.', ff.ext) as file_name, ff.ext, ff.file_type_id, ft.name as file_type, ft.path, fia.sort
                FROM " . $prefix . $this->table . " as ia
                left join " . $prefix . "files_image_albums as fia using(" . $this->primary_key . ") 
                left join " . $prefix . "storage_files as f using(file_id)
                left join " . $prefix . "file_formats as ff using(file_format_id)
                left join " . $prefix . "file_types as ft using(file_type_id) 
                WHERE image_album_id = " . (int) $album_id . " AND file_id IS NOT NULL
                ORDER BY album_sort, fia.sort;";
        $result = $this->db->query($sql)->result_array();

        if ($return_tree) {
            $tree = array();
            foreach ($result as $arr) {
                if (!isset($tree[$arr[$this->primary_key]]))
                    $tree[$arr[$this->primary_key]] = array(
                        $this->primary_key => $arr[$this->primary_key],
                        'album_name' => $arr['album_name'],
                        'album_description' => $arr['album_description'],
                        'file_category_id' => $arr['file_category_id'],
                        'object_id' => $arr['object_id'],
                    );
                $tree[$arr[$this->primary_key]]['images'][] = $arr;
            }
            return $tree;
        }

        return $result;
    }

    /**
     * Clear current album - delet all links to images from album
     * !! files not delete
     * @param int $album_id
     */
    private function clear_album($album_id) {
        $prefix = $this->get_table_prefix();
        // delete involves with files from album
        $this->db->query("
            DELETE FROM {$prefix}file_involves
            WHERE file_involve_id IN (
                SELECT file_involve_id FROM {$prefix}files_image_albums 
                WHERE {$this->primary_key} = " . (int) $album_id . "
                );");
    }

    /**
     * Update album images
     * @param int $album_id
     * @param array $data - data for current album images
     * @example update_album_images(1, array(array('file_id' => 0, 'sort' => 0)) );
     */
    public function update_album_images($album_id, $data) {
        $this->clear_album($album_id);
        foreach ($data as $item) {
            if (!isset($item['file_id']))
                continue;
            // insert involves
            // prepare data
            $row = array(
                $this->primary_key => (int) $album_id,
                'file_id' => (int) $item['file_id'],
                'sort' => element('sort', $item, $this->defaul_sort_index),
                'file_involve_id' => element('file_involve_id', $item, 0)
            );
            $this->db->insert('files_image_albums', $row);
        }
    }

    /**
     * Delete album
     * @param int $id
     * @todo check deleted
     * @return bool 
     */
    public function delete_by_primary_key($album_id) {
        $album = $this->get_by_primary_key($album_id);
        // album not found
        if (empty($album))
            return FALSE;

        $file_category_id = (int) $album['file_category_id'];

        // get images by album
        $album_images = $this->get_with_images_by_id($album_id);

        if (!empty($album_images)) {
            // check file involves & delete
            // delete albom relations
            $this->clear_album($album_id);
        }

        // delete album
        parent::delete_by_primary_key($album_id);
        return TRUE;
    }    
}
