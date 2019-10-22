<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Organizations model
 *
 * @date 09.10.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Organizations_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'organizations';
        $this->primary_key = 'organization_id';
    }

    /**
     * Get organizations types
     * @return array
     */
    public function get_types($filters = array()) {
        return $this->get_all(array_merge($filters, array('table' => 'organization_types')));
    }

    /**
     * Search
     * @param array $filters
     * @param bool $is_row
     * @return array
     */
    public function search($filters, $is_row = FALSE) {

        $t = 'o';
        $prefix = $this->get_table_prefix();
        $joins = $having = '';
        $conditions = array();

        $limit = (int) element('limit', $filters, $this->limit);
        $offset = is_numeric(($offset = element_strict('offset', $filters, FALSE))) && $offset >= 0 ? "LIMIT {$offset}, {$limit}" : "";
        $order = ($order = element('order', $filters)) !== FALSE ? "ORDER BY " . $this->db->escape_str($order) . " {$this->order_direction(element('order_direction', $filters, 'ASC'))}" : "ORDER BY created DESC";

        $conditions[] = "AND {$t}.status != " . self::STATUS_DELETED;
        
        if (element($this->primary_key, $filters))
            $conditions[] = "AND " . $this->primary_key . " = " . (int) $filters[$this->primary_key];

        // organization_type_id
        if (element('organization_type_id', $filters)) {
            $joins .= "LEFT JOIN {$prefix}organizations_organization_types USING(organization_id)";
            if (is_numeric($filters['organization_type_id'])) {
                $conditions[] = "AND organization_type_id = " . (int) $filters['organization_type_id'];
            } elseif (is_string($filters['organization_type_id']) && $filters['organization_type_id'] === 'all') {
                $having .= "AND organization_types = (SELECT GROUP_CONCAT(`name` SEPARATOR ', ') FROM {$prefix}organization_types)";
            } elseif (is_array($filters['organization_type_id'])) {
                if (in_array('all', $filters['organization_type_id'])) {
                    $having .= "AND organization_types = (SELECT GROUP_CONCAT(`name` SEPARATOR ', ') FROM {$prefix}organization_types)";
                } else {
                    $conditions[] = "AND organization_type_id IN (" . implode_int(',', $filters['organization_type_id']) . ")";
                }
            }
        }

        if (element('alias', $filters))
            $conditions[] = "AND {$t}.alias = '" . xss_clean($filters['alias']) . "'";

        if (element('name_eq', $filters))
            $conditions[] = "AND {$t}.name = '" . xss_clean($filters['name_eq']) . "'";

        // search
        if (element('name', $filters, FALSE))
            $conditions[] = "AND {$t}.name like '%" . trim($filters['name']) . "%' ";
        // data range
        if (element('date_begin', $filters, FALSE))
            $conditions[] = "AND DATE({$t}.created) >= " . $this->escape(date('Y-m-d', strtotime($filters['date_begin'])));
        if (element('date_end', $filters, FALSE))
            $conditions[] = "AND DATE({$t}.created) <= " . $this->escape(date('Y-m-d', strtotime($filters['date_end'])));

        // tags
        if (element('tags', $filters, FALSE)) {
            $tags = explode('|', $filters['tags']);
            $joins .= " left join {$prefix}organizations_tags using(organization_id)";
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
            $conditions[] = "AND ({$tmp_condition})";
        }
        
        // name_like
        if (array_get($filters, 'name_like')) {
            if (is_numeric($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.'. $this->primary_key .' = ' . (int) $filters['name_like'];
            } elseif (is_string($filters['name_like'])) {
                $conditions[] = 'AND ' . $t . '.name LIKE "%' . $this->db->escape_str($filters['name_like']) . '%"';
            }
        }
        // status
        if (array_get($filters, 'status'))
            if (is_array($filters['status']))
                $conditions[] = "AND {$t}.status IN (" . implode_int(',', $filters['status']) . ")";
            else
                $conditions[] = "AND {$t}.status = " . (int) $filters['status'];

        $conditions = !empty($conditions) ? 'WHERE ' . ltrim(implode(' ', $conditions), 'AND') : '';

        $having = !!$having ? "HAVING " . trim($having, " AND") : $having;

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$t}.*, concat(ft.path, f.name, '.', ff.ext) as image,
                (
                    SELECT GROUP_CONCAT(`name` SEPARATOR ', ')
                    FROM {$prefix}organization_types 
                    LEFT JOIN {$prefix}organizations_organization_types USING(organization_type_id)
                    WHERE organization_id = {$t}.organization_id
                ) AS organization_types
                FROM {$prefix}organizations as {$t}
                LEFT JOIN {$prefix}storage_files AS f USING(file_id)
                LEFT JOIN {$prefix}file_formats AS ff USING(file_format_id)
                LEFT JOIN {$prefix}file_types AS ft USING(file_type_id)
                {$joins} {$conditions} GROUP BY organization_id {$having} {$order} {$offset};";
//        vdump($sql, 1);
//        vdump($filters, 1);

        if ($is_row) {
            $result = $this->db->query($sql)->row_array();
            if ($result)
                $result['params'] = json_decode(element('params', $result, '{}'), TRUE);
            return $result;
        }

        return $this->db->query($sql)->result_array();
    }

    /**
     * Set tags 
     * @param array/int $organisation
     * @param array $tags
     */
    public function set_tags($organisation, $tags) {
        $id = is_array($organisation) ? (int) element($this->primary_key, $organisation, 0) : (int) $organisation;
        // drop old tags
        $this->db->where($this->primary_key, $id)->delete('organizations_tags');
        // set new tags
        foreach ($tags as $tag) {
            $this->db->insert('organizations_tags', array($this->primary_key => $id, 'tag_id' => (int) element('tag_id', $tag, 0)));
        }
    }

    /**
     * Get tags
     * @param array/int $organisation
     * @return array
     */
    public function get_tags($organisation, $only_name = TRUE) {
        $id = is_array($organisation) ? (int) element($this->primary_key, $organisation, 0) : (int) $organisation;
        $prefix = $this->get_table_prefix();
        // fields
        $fileds = $only_name ? 't.name' : 't.name, t.tag_id, t.alias';

        $sql = "SELECT {$fileds} FROM {$prefix}organizations_tags 
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
     * Set organisation types
     * @param array/int $organisation
     * @param array $organisation_type_ids - array(int, int)
     */
    public function set_organization_types($organisation, $organisation_type_ids) {
        $id = is_array($organisation) ? (int) element($this->primary_key, $organisation, 0) : (int) $organisation;
        // drop old types
        $this->db->where($this->primary_key, $id)->delete('organizations_organization_types');
        // set new types
        foreach ($organisation_type_ids as $type_id) {
            if ($type_id)
                $this->db->insert('organizations_organization_types', array(
                    $this->primary_key => $id,
                    'organization_type_id' => (int) $type_id
                ));
        }
    }

    /**
     * Add organization type
     * @param array/int $organisation
     * @param int $organisation_type_id
     */
    public function add_organization_type($organization, $organization_type_id) {
        $id = is_array($organization) ? (int) element($this->primary_key, $organization, 0) : (int) $organization;

        $check_relation = $this->db
                        ->where($this->primary_key, $id)
                        ->where('organization_type_id', (int) $organization_type_id)
                        ->get('organizations_organization_types')->row_array();

        if ($organization_type_id && empty($check_relation)) {
            $this->db->insert('organizations_organization_types', array(
                $this->primary_key => $id,
                'organization_type_id' => (int) $organization_type_id
            ));
        }
    }

    /**
     * get organisation types
     * @param array/int $organisation
     * @return array
     */
    public function get_organization_types($organisation) {
        $id = is_array($organisation) ? (int) element($this->primary_key, $organisation, 0) : (int) $organisation;
        $prefix = $this->get_table_prefix();

        // fields
        $fileds = 't.name, t.organization_type_id';

        $sql = "SELECT {$fileds} FROM {$prefix}organizations_organization_types 
                LEFT JOIN {$prefix}organization_types AS t USING(organization_type_id)
                WHERE {$this->primary_key} = {$id}";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get organizations by type
     * @param int $organisation_type_id
     * @return arry
     */
    public function get_by_type($organisation_type_id) {
        $prefix = $this->get_table_prefix();

        $sql = "SELECT * FROM {$prefix}organizations_organization_types
                LEFT JOIN {$prefix}organizations USING(organization_id)
                WHERE organization_type_id = " . (int) $organisation_type_id . " ORDER BY {$prefix}organizations.name;";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get by object id
     * @param int $object_id
     * @return array
     */
    public function get_by_object($object_id) {
        $prefix = $this->get_table_prefix();
        $sql = "SELECT o.*, t.name as organization_type, organization_type_id, concat(ft.path, f.name, '.', ff.ext) as image
                FROM {$prefix}main_objects_organizations
                LEFT JOIN {$prefix}organizations AS o using(organization_id)
                LEFT JOIN {$prefix}organization_types AS t using(organization_type_id)
                LEFT JOIN {$prefix}storage_files AS f USING(file_id)
                LEFT JOIN {$prefix}file_formats AS ff USING(file_format_id)
                LEFT JOIN {$prefix}file_types AS ft USING(file_type_id)
                WHERE object_id = " . (int) $object_id . ";";
        $result = $this->db->query($sql)->result_array();

        foreach ($result as $key => $it)
            $result[$key]['params'] = json_decode($it['params'], TRUE);

        return $result;
    }

}
