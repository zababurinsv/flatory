<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_contacts extends CI_Model {
    
    public function contacts_edit_db ($contacts_data){
        $this->db->where('user_id',1);
        $this->db->update('users',$contacts_data);
    }
    public function personal_infomation_db ($personal_data){
        $this->db->where('user_id',1)->update('users',$personal_data);
    }
    
}