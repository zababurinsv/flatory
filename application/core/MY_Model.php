<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MY_Model
 *
 * @date 21.02.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Model extends CI_Model {

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_NOT_PUBLISHED = 2;
    const STATUS_ARCHIVE = 3;

    public $limit = 10;
    protected $table = NULL;
    protected $primary_key = NULL;
    private $_ci;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Получаем количество без лимита
     * @return int
     */
    public function found_rows() {
        $count = $this->db->query('SELECT FOUND_ROWS();')->row_array();
        return (int) element('FOUND_ROWS()', $count, 0);
    }

    public function get_by_id($table, $id) {
        $primary_key = $this->_primary_key_from_table_name($table);
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($primary_key, (int) $id);
        return $this->db->get()->row_array();
    }

    /**
     * update by id
     * @param string $table - table name
     * @param mixed $id - current id value
     * @param array $data - data for updating
     * @return bool
     */
    public function update_by_id($table, $id, $data) {
        $primary_key = $this->_primary_key_from_table_name($table);
        $this->db->where($primary_key, $id);
        return $this->db->update($table, $data);
    }

    /**
     * delete by id
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function delete_by_id($table, $id) {
        $primary_key = $this->_primary_key_from_table_name($table);
        $this->db->where($primary_key, $id);
        return $this->db->delete($table);
    }

    /**
     * Insert statement
     * @param array $data
     * @param string $table
     * @return int - inserted id
     */
    public function insert($data, $table = FALSE) {
        $table = !$table ? $this->table : $table;
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    private function _primary_key_from_table_name($table) {
        if (!is_null($this->primary_key))
            return $this->primary_key;
        $primary_key = substr($table, -1) === 's' ? substr($table, 0, strlen($table) - 1) : $table;
        return $primary_key . '_id';
    }

    /**
     * Получить список
     * @param int $status
     * @param int $offset
     * @param string $order
     * @param string $order_direction
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function get_list($status = self::STATUS_ACTIVE, $offset = FALSE, $order = FALSE, $order_direction = 'ASC', $limit = FALSE) {
        $limit = !$limit ? $this->limit : $limit;

        if ($this->table === NULL)
            throw new Exception('Table is not defined!');
        $this->db->ar_found_rows = TRUE;

//        $this->db->select('*');
        $this->db->from('cat_city');
        $this->db->from('handbks');

        $this->db->from($this->table);
        if (is_numeric($status))
            $this->db->where('status', $status);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }
        if ($order !== FALSE){
            $this->db->order_by($order, $order_direction);
        }else{
//
            $result = $this->db->get()->result_array();
        }
        return $result;
    }

    /**
     * Get primary
     * @return string
     */
    public function get_primary_key() {
        return $this->primary_key;
    }

    /**
     * Get by primary key
     * @param int $id
     * @return array
     */
    public function get_by_primary_key($id) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->primary_key, (int) $id);
        return $this->db->get()->row_array();
    }

    /**
     * Get by primary keys
     * @param array $ids
     * @return array
     */
    public function get_by_primary_keys($ids) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where_in($this->primary_key, $ids);
        return $this->db->get()->result_array();
    }

    /**
     * Update by primary key
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update_by_primary_key($id, $data) {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Get by field
     * @param string $field - field name
     * @param string/array $value - value (array -> where in)
     * @param bool $is_row - return one row [defaul: TRUE]
     * @return array
     */
    public function get_by_field($field, $value, $is_row = TRUE) {

        $this->db->select('*');
        $this->db->from($this->table);


//        var_dump('gggg');die;

//        var_dump('select *');
//        var_dump('from ');
//        var_dump($this->table);
//        var_dump('where_in');
//        var_dump($field);
//        var_dump($value);die;
//

        if (is_array($value)){
            $this->db->where_in($field, $value);
        }else{
            $this->db->where($field, $value);
        }
        if ($is_row){
            return $this->db->get()->row_array();
        }else{
            return $this->db->get()->result_array();
        }
    }

    /**
     * Get table prefix
     * @param string $table
     * @return string 
     */
    public function get_table_prefix($table = NULL) {
        return !$table ? $this->db->dbprefix : $this->db->dbprefix($table);
    }

    /**
     * Delete by primary key
     * @param type $id
     */
    public function delete_by_primary_key($id) {
        $this->db->delete($this->table, array($this->primary_key => $id));
    }

    /**
     * order direction
     * @param string $direction - asc/desc
     * @return string - ASC/DESC (uppercase) [DEFAULT: ASC]
     */
    protected function order_direction($direction) {
        $d = array('asc' => 'ASC', 'desc' => 'DESC');
        return array_get($d, strtolower($direction), 'ASC');
    }

    /**
     * Escape string
     * @param string $str
     * @return string
     */
    public function escape($str) {
        return $this->db->escape($str);
    }

    /**
     * load model - alternative for $this->load->model() (it's not work in mode)
     * @param string $model - model name
     */
    public function load_model($model) {
        $this->_ci = & get_instance();
        $this->_ci->load->model($model);
        $this->$model = $this->_ci->$model;
    }

    /**
     * Get table columns
     * @param string $table - table name - pgsql: 'schema.table_name', mysql: table_name
     * @return array
     */
    public function get_table_columns($table = FALSE) {
        $table = !$table ? $this->table : $table;
        if (!$table)
            return array();

        $table_schema = '';
        // in mysql table_schema is the database name
        $table_schema = $this->get_database_name();

        $table_schema = !!$table_schema ? " AND table_schema = '" . $table_schema . "'" : "";
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $this->db->dbprefix . $table . "'" . $table_schema . ";";

        $result = $this->db->query($sql)->result_array();

        if (!empty($result))
            $result = simple_tree($result, 'column_name');
        return array_keys($result);
    }

    /**
     * Get current database name
     * @return string/bool
     */
    public function get_database_name() {
        $result = array();
        $result = $this->db->query('SELECT DATABASE() AS db_name;')->row_array();
        return element('db_name', $result, FALSE);
    }

    /**
     * Get all rows from table
     * @param array $filters:<br>
     * <p><b>where</b> array - list of conditions.</p>
     * <p><b>where_not</b> array - list of not equal conditions.</p>
     * @return array
     * @throws Exception
     */
    public function get_all($filters = array()) {

        $limit = element('limit', $filters, $this->limit);
        $table = element('table', $filters, $this->table);
        $status = element('status', $filters);
        $offset = element_strict('offset', $filters);
        $order = element('order', $filters);
        $where = element('where', $filters, array());
        $where_not = is_array(array_get($filters, 'where_not')) ? $filters['where_not'] : [];
        $like = element('like', $filters, array());
        $order_direction = strtolower(element('order_direction', $filters, 'asc')) === 'asc' ? 'asc' : 'desc';
        
        if ($table === NULL)
            throw new Exception('Table is not defined!');

        $this->db->ar_found_rows = TRUE;
        $this->db->select('*');
        $this->db->from($table);

        if (is_numeric($status))
            $this->db->where('status', $status);

        // where condition
        if (is_array($where) && !empty($where))
            foreach ($where as $condition)
                foreach ($condition as $field => $value)
                    if (is_array($value))
                        $this->db->where_in($field, $value);
                    else
                        $this->db->where($field, $value);

        // where_not condition
        if (is_array($where_not) && !!$where_not)
            foreach ($where_not as $condition)
                foreach ($condition as $field => $value)
                    if (is_array($value))
                        $this->db->where_not_in($field, $value);
                    else
                        $this->db->where($field . ' !=', $value);

        // like condition
        if (is_array($like) && !empty($like))
            foreach ($like as $condition)
                foreach ($condition as $field => $value)
                    $this->db->like($field, $value);

        if ($offset !== FALSE && $offset !== -1) {
            $this->db->limit($limit, $offset);
        }

        if ($order !== FALSE)
            $this->db->order_by($order, $order_direction);

        $result = element('is_row', $filters) ? $this->db->get()->row_array() : $this->db->get()->result_array();
        
//        vdump($this->db->last_query(), 1);
        
        return $result;
    }

    /**
     * Get table
     * @return string
     */
    public function get_table() {
        return $this->table;
    }
    
    /**
     * get status list
     * @return array
     */
    public function get_status_list() {
        return [
            self::STATUS_ACTIVE => ['alias' => 'active', 'title' => 'Опубликовано'],
            self::STATUS_NOT_PUBLISHED => ['alias' => 'not-published', 'title' => 'Черновик'],
        ];
    }

}
