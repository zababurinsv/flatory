<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Articles controller
 */
class Articles extends MY_FrontController {

    /**
     * category
     * @var array 
     */
    private $_category;

    /**
     * Model default
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
        $this->load->model('File_Categories');
        $this->_category = $this->File_Categories->get_by_field('prefix', 'article');
//        var_dump('ddddd');die;
        if (empty($this->_category))
            show_404();
    }

    /**
     * News list
     */
    public function index() {

        // load librares
        $this->load->library('flpagination');

        $this->flpagination->set_limit(10);

        $list = $this->Posts_Model->search(array(
            'file_category_id' => $this->_category['file_category_id'],
            'offset' => $this->flpagination->get_offset(),
            'limit' => $this->flpagination->get_limit(),
            'status' => MY_Model::STATUS_ACTIVE,
        ));
                
        $count = $this->Posts_Model->found_rows();

        $pagination = $this->flpagination->pagination(array(
            'total_rows' => $count,
            'base_url' => '/news',
        ));

        $this->title .= ' - Новости рынка недвижимости Москвы и Подмосковья';
        $this->meta_description = 'Строительные новости Москвы и области. Мнения специалистов и комментарии участников рынка.';

        $this->body = $this->load->view($this->template_dir . 'pages/posts', array(
            'list' => $list,
            'pagination' => $pagination,
            'category' => $this->_category,
                ), TRUE);
        $this->render();
    }

    /**
     * News item
     * @param string $alias
     * @param bool $is_preview - is preview mode - only for admin
     */
    public function articles_item($alias, $is_preview = FALSE) {
        
        $item = $this->Posts_Model->search(array(
            'alias' => $alias,
            'file_category_id' => $this->_category['file_category_id'],
            'status' => $is_preview === TRUE ? FALSE : MY_Model::STATUS_ACTIVE,
                ), TRUE);

        if (!$item)
            show_404();

        $this->load->library('Widget_gallery', array('this' => $this));

        // album create      
        $item['content'] = $this->widget_gallery->replace_gallery_marks($item['content']);

        $this->title = element('title', $item, $item['name']);
        $this->meta_description = element('description', $item, '');
        $this->meta_keywords = element('keywords', $item, '');

        $tags = $this->Posts_Model->get_post_tags($item, FALSE);

        $read_more = !empty($tags) ? $this->Posts_Model->search(array(
                    'tag_id' => array_keys(simple_tree($tags, 'tag_id')),
                    'order' => 'created',
                    'order_direction' => 'DESC',
                    'limit' => 5,
                    'offset' => 0,
                    'not_post_id' => element('post_id', $item),
                )) : array();
        
        $categories = !empty($read_more) ? $this->File_Categories->get_by_field('prefix', $this->Posts_Model->get_types(), FALSE) : array();
        if(is_array($categories) && !empty($categories))
            $categories = simple_tree ($categories, 'file_category_id');
        
        $this->body = $this->load->view($this->template_dir . 'pages/posts_item', array(
            'item' => $item,
            'tags' => $tags,
            'category' => $this->_category,
            'read_more' => $read_more,
            'categories' => $categories,
                ), TRUE);

        $this->set_scripts_bottom('../jquery.jcarousel.min.js');
        $this->set_styles('../fancybox/source/jquery.fancybox.css');
        $this->set_scripts(array('../fancybox/source/jquery.fancybox.pack.js', '../fl/fl_gallery.js'));
        $this->render();
    }
    
    /**
     * Preview news
     * @param string $alias
     */
    public function preview($alias) {
        session_start();
        if (!isset($_SESSION['login_ok']))
            show_404 ();
        
        $this->articles_item($alias, TRUE);
    }

}
