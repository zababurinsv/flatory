<?php

/**
 * Controller Tag
 *
 * @date 04.08.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Tags  extends MY_FrontController{
   
    
    /**
     * Model tags
     * @var \Tags_Model 
     */
    public $Tags_Model;
    
    /**
     * Model post model
     * @var \Posts_Model 
     */
    public $Posts_Model;
    
    /**
     * Model categories
     * @var \File_Categories 
     */
    public $File_Categories;
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model('Posts_Model');
        $this->load->model('Tags_Model');
        $this->load->model('File_Categories');
    }
    
    public function index($alias = FALSE){
        $alias = xss_clean($alias);
        if(! ($tag = $this->Tags_Model->get_by_field('alias', $alias)))
            show_404 ();
        
        $posts = $this->Posts_Model->search(array(
            'status' => MY_Model::STATUS_ACTIVE,
            'tag_id' => element('tag_id', $tag),
        ));
        $count = $this->Posts_Model->found_rows();
        
        $categories = $this->File_Categories->get_by_field('prefix', $this->Posts_Model->get_types(), FALSE);
        if(is_array($categories) && !empty($categories))
            $categories = simple_tree ($categories, 'file_category_id');
        
        $this->title .= ' ' . element('name', $tag, '');
        
        $this->body = $this->load->view($this->template_dir . 'pages/tags', array(
            'tag' => $tag,
            'list' => $posts,
            'categories' => $categories,
                ), TRUE);
        
        $this->render();
        
    }
}
