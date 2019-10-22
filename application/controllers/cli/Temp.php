<?php

if (PHP_SAPI !== 'cli')
    exit('No web access allowed');

class Temp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('log');
    }

    public function testt() {

        var_dump($this->log);
        $this->log->write_cli_log('dddddddddd');
    }
    /**
     * перенос cat_distance_to_metro -> cat_meta_metro
     */
    public function meta_metro() {
        $this->_line('Start ' . __FUNCTION__);

        $old = $this->db->query("select * from cat_distance_to_metro
                    left join cat_meta_metro on id_object = object_id and id_metro = metro_id")->result_array();

        foreach ($old as $it) {
            if (array_get($it, 'object_id') && array_get($it, 'metro_id')) {
                // update
                $this->db->where('object_id', $it['object_id'])->where('metro_id', $it['metro_id'])->update('meta_metro', [
                    'distance' => $it['name'],
                    'walking_time' => $it['min'],
                    'drive_time' => $it['car'],
                ]);
            } else {
                // insert
                $this->db->insert('meta_metro', array(
                    'object_id' => $it['id_object'],
                    'metro_id' => $it['id_metro'],
                    'distance' => $it['name'],
                    'walking_time' => $it['min'],
                    'drive_time' => $it['car'],
                ));
            }
        }

        $this->_line('End ' . __FUNCTION__);
    }

    /**
     * print line
     * @param string $str
     */
    private function _line($str) {
        echo $str . PHP_EOL;
        $this->log->write_cli_log($str, __CLASS__);
    }

    public function glossary_building_lot() {
       
        $this->_line('Start ' . __FUNCTION__);
        
        
       $rel = $this->db->query("SELECT glossary_id, registry_id, g.object_id
                            FROM cat_glossary as g
                            left join cat_building_lot as bl on g.object_id = bl.id
                            left join cat_registry as r on r.name = bl.name and r.handbk_id = g.handbk_id
                            where g.handbk_id = 2;")->result_array();
       
       foreach ($rel as $it){
           $this->db->set('object_id', array_get($it, 'registry_id'))->where('glossary_id', $it['glossary_id'])->update('glossary');
           $this->_line("object_id: " . $it['object_id'] . " -> " . ($it['registry_id'] ? $it['registry_id'] : 'Null' ));
       }
        
       $this->_line('End ' . __FUNCTION__);
    }
    
    public function glossary_type_of_building() {
       
        $this->_line('Start ' . __FUNCTION__);
        
        
       $rel = $this->db->query("SELECT glossary_id, registry_id, g.object_id
                                FROM cat_glossary as g
                                left join cat_type_of_building as bl on g.object_id = bl.id
                                left join cat_registry as r on r.name = bl.name and r.handbk_id = g.handbk_id
                                where g.handbk_id = 1;")->result_array();
       
       foreach ($rel as $it){
           $this->db->set('object_id', array_get($it, 'registry_id'))->where('glossary_id', $it['glossary_id'])->update('glossary');
           $this->_line("object_id: " . $it['object_id'] . " -> " . ($it['registry_id'] ? $it['registry_id'] : 'Null' ));
       }
        
       $this->_line('End ' . __FUNCTION__);
    }

    public function object_sections() {

        $this->_line('Start ' . __FUNCTION__);

        $objects = $this->db->get('main_object')->result_array();
        $sections = $this->db->where('is_default', 0)->get('object_sections')->result_array();

        foreach ($objects as $it) {

            $this->_line('Obj: ' . $it['id']);
            
            foreach ($sections as $sec) {
                
                $this->_line('  Sect: ' . $sec['object_section_id']);
                
                $this->db->insert('object_sections_has_main_object', [
                    'object_section_id' => $sec['object_section_id'],
                    'object_id' => $it['id'],
                    'created' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->_line('End ' . __FUNCTION__);
    }
}
