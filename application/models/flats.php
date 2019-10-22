<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Модель для работы с квартирами
 * @date 13.09.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Flats extends CI_Model {

//    public function Flats() {
//        parent::Model();
//    }
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Save rooms
     * @param array $rooms - array rooms 
     * @return array
     * 
     * @example Flats->save_rooms(array(0 => 1, 1 => 2));
     */
    public function save_rooms($rooms, $object_id) {
        // get old rooms
        $old_rooms = collect_array_by_key($this->db->select('room_id')->where('object_id', $object_id)->get('flats')->result_array(), 'room_id', TRUE);
        
        $flats = array();
        if (!empty($rooms))
            foreach ($rooms as $room) {
                $key_old_rooms = array_search($room, $old_rooms);
                // save current if not included to $old_rooms
                if ($key_old_rooms === FALSE) {
                    $flats[] = array(
                        'object_id' => (int) $object_id,
                        'room_id' => (int) $room,
                    );
                } else {
                    // unset (remain only that which will remove)
                    unset($old_rooms[$key_old_rooms]);
                }
            }

        // delete old rooms, that were deleted in dashboard
        if (!empty($old_rooms))
            foreach ($old_rooms as $room_id) {
                $this->db->delete('flats', array('object_id' => (int) $object_id, 'room_id' => (int) $room_id));
            }
        // save new rooms
        if (!empty($flats))
            foreach ($flats as $flat) {
                $this->db->insert('flats', $flat);
            }
            
        return array('add' => count($flats), 'del' => count($old_rooms));
    }
    
    /**
     * Get rooms by object_id
     * @param int $object_id
     * @return array
     */
    public function get_rooms_by_object_id($object_id){
        return collect_array_by_key($this->db->select('room_id')->where('object_id', (int)$object_id)->get('flats')->result_array(), 'room_id', TRUE);
    }
    
    /**
     * Get flats by object_id
     * @param int $object_id
     * @param array  $filters : <br>
     * <b>cost_exitst</b> - bool - цены заданы<br>
     * @return array
     */
    public function get_flats_by_object_id($object_id, array $filters = []){
        $this->db->join('rooms', 'flats.room_id = rooms.room_id', 'left')->where('object_id', (int)$object_id);
        
        // cost_exitst - цены заданы
        if(array_get($filters, 'cost_exitst'))
            $this->db->where('(cost_m_min OR cost_m_max OR cost_min OR cost_max)');
        
        return $this->db->order_by("flats.room_id", "asc")->get('flats')->result_array();
    }
    
    /**
     * Update room by object_id and room_id
     * @param array $room - room data (must include object_id and room_id)
     * @throws Exception
     */
    public function update_room_by_object_id_room_id($room){
        $object_id = (int)element('object_id', $room, 0);
        $room_id = (int)element('room_id', $room, 0);
        
        unset($room['object_id']);
        unset($room['room_id']);
        
        if($object_id === 0 || $room_id === 0)
            throw new Exception('Incorrect params! object_id, room_id - must be > 0');
        
        $this->db->where('object_id', $object_id);
        $this->db->where('room_id', $room_id);
        $this->db->update('flats', $room); 
    }
    
}
