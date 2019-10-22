<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 015_coordination
 *
 * @date 13.06.2016
 */
class Migration_dbclean extends CI_Migration {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function up() {
        $this->_coords();
        $this->_route();
        $this->_tech_comments();
        $this->_floors();
        $this->_ceiling_height();
        $this->_number_of_sec();
        $this->_infrastructure();
    }

    private function _coords() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `x` INT(11) NOT NULL DEFAULT 0 AFTER `publish`,
                ADD COLUMN `y` INT(11) NOT NULL DEFAULT 0 AFTER `x`,
                ADD COLUMN `point` TEXT NULL AFTER `y`;");

        $obj_coords = $this->db->select('m.id, c.x, c.y, c.point')
                ->from('meta as m')
                ->join('coordination as c', 'c.id = m.id_coordination', 'left')
                ->get()
                ->result_array();

        foreach ($obj_coords as $c) {
            $this->db->where('id', $c['id'])->update('meta', [
                'x' => $c['x'],
                'y' => $c['y'],
                'point' => $c['point'],
            ]);
        }
    }
    
    private function _route() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `bus` TEXT NULL AFTER `point`,
                ADD COLUMN `auto` TEXT NULL AFTER `bus`;");

        $list = $this->db->select('m.id, r.bus, r.auto')
                ->from('meta as m')
                ->join('route as r', 'r.id = m.id_route', 'left')
                ->get()
                ->result_array();

        foreach ($list as $it) {
            $this->db->where('id', $it['id'])->update('meta', [
                'bus' => $it['bus'],
                'auto' => $it['auto'],
            ]);
        }
    }
    
    private function _tech_comments() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `garage_comment` TEXT NULL AFTER `auto`,
                ADD COLUMN `protection_comment` TEXT NULL AFTER `garage_comment`;");

        $list = $this->db->select('m.id, tc.garage_comment, tc.protection_comment')
                ->from('meta as m')
                ->join('tech_comments as tc', 'tc.id_object = m.id_object', 'left')
                ->get()
                ->result_array();

        foreach ($list as $it) {
            $this->db->where('id', $it['id'])->update('meta', [
                'garage_comment' => $it['garage_comment'],
                'protection_comment' => $it['protection_comment'],
            ]);
        }
    }
    
    private function _floors() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `floor_begin` INT(11) NULL AFTER `protection_comment`,
                ADD COLUMN `floor_end` INT(11) NULL AFTER `floor_begin`;");

        $obj_coords = $this->db->select('m.id, f.ot, f.do')
                ->from('meta as m')
                ->join('floors as f', 'f.id = m.id_floors', 'left')
                ->get()
                ->result_array();

        foreach ($obj_coords as $o) {
            $this->db->where('id', $o['id'])->update('meta', [
                'floor_begin' => $o['ot'],
                'floor_end' => $o['do'],
            ]);
        }
    }
    
    private function _ceiling_height() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `ceiling_height` DECIMAL(10,2) NULL AFTER `floor_end`;");

        $obj_coords = $this->db->select('m.id, f.number as ceiling_height')
                ->from('meta as m')
                ->join('ceiling_height as f', 'f.id = m.id_ceiling_height', 'left')
                ->get()
                ->result_array();

        foreach ($obj_coords as $o) {
            $this->db->where('id', $o['id'])->update('meta', [
                'ceiling_height' => $o['ceiling_height'],
            ]);
        }
    }
    
    private function _number_of_sec() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `number_of_sec` INT(11) NULL AFTER `ceiling_height`;");

        $obj_coords = $this->db->select('m.id, f.number as number_of_sec')
                ->from('meta as m')
                ->join('number_of_sec as f', 'f.id = m.id_number_of_sec', 'left')
                ->get()
                ->result_array();

        foreach ($obj_coords as $o) {
            $this->db->where('id', $o['id'])->update('meta', [
                'number_of_sec' => $o['number_of_sec'],
            ]);
        }
    }
    
    private function _infrastructure() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD COLUMN `infrastructure` TEXT NULL AFTER `number_of_sec`;");

        $list = $this->db->select('m.id, tc.text as infrastructure')
                ->from('meta as m')
                ->join('infrastructure as tc', 'tc.id_object = m.id_object', 'left')
                ->get()
                ->result_array();

        foreach ($list as $it) {
            $this->db->where('id', $it['id'])->update('meta', [
                'infrastructure' => $it['infrastructure'],
            ]);
        }
    }

    public function down() {
        
    }

}
