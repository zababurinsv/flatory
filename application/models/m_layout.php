<?php
class m_layout extends CI_Model {

    public function construct($view_name,$param){
        $param['seo'] = isset( $param['seo'] ) && $param['seo'] ? $param['seo'] : array( 'title' => '', 'description' => '', 'keywords' => '' );
         $this->load->model('Geo');
         $this->load->model('Search_Model');
        $this->load->view('header',array( 'seo' => $param['seo'] ));
        $search_array = $this->get_search_data();
        $this->load->view('v_index',array('name'=>$view_name,'data'=>$param, 'search'=>$search_array));
        $this->load->view('footer');
    }

    public function get_search_data(){
        
        $search = array();
        // search xone group list
        $search['zone'] = $this->Geo->zone;
        // decorate zone
        foreach ($search['zone'] as $key => $item){
            if($item->code === 'MOS')
               $search['zone'][$key]->name = 'Область'; 
        }
        krsort($search['zone']);
        $search['geo_direction'] = $this->Geo->get_directions();
        $search['district'] = $this->Geo->get_locality('MOW');

        // max filters values
        $max_filters = $this->Search_Model->get_max_filters($this->Geo->get_zone_ids());
        $search['max_filters'] = json_encode($max_filters);
        // mkad distans filter list
        $search['distance_to_mkad'] = $this->db->get('distance_to_mkad')->result();

        return $search;
    }
}