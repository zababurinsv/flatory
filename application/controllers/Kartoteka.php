<?php

/**
 * kartoteka controller
 *
 * @date 13.09.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Kartoteka extends MY_FrontController {

    /**
     * category
     * @var array 
     */
    private $_category;

    /**
     * model Glossary_Model 
     * @var \Glossary_Model 
     */
    public $Glossary_Model;

    /**
     * Model categories
     * @var \File_Categories 
     */
    public $File_Categories;

    /**
     * Model organizations
     * @var \Organizations_Model 
     */
    public $Organizations_Model;

    public function __construct() {
        parent::__construct();

        $this->load->model('Glossary_Model');
        $this->load->model('File_Categories');
        $this->load->model('Organizations_Model');

        $this->_category = $this->File_Categories->get_by_field('prefix', 'glossary');
    }

    public function index() {
        $this->title .= ' Картотека.';
        // @todo pagination ??
//        $list = $this->Glossary_Model->search(array(
//            'parent_id' => 0,
//            'status' => MY_Model::STATUS_ACTIVE,
//        ));
//        $list_tree = simple_tree($list, $this->Glossary_Model->get_primary_key());

//        $childs = $this->Glossary_Model->search(array(
//            'parent_id' => !empty($list_tree) ? array_keys($list_tree) : FALSE,
//            'status' => MY_Model::STATUS_ACTIVE,
//        ));

//        foreach ($childs as $it) {
//            $list_tree[$it['parent_id']]['childs'][] = $it;
//        }
//        $organization_types = simple_tree($this->Organizations_Model->get_types(), 'name');
//        $builders = $this->Organizations_Model->get_by_type(element('organization_type_id', element('Застройщик', $organization_types, array())));
//        $sellers = $this->Organizations_Model->get_by_type(element('organization_type_id', element('Продавец', $organization_types, array())));
        $this->body = $this->load->view($this->template_dir . 'pages/kartoteka', array(
//            'list' => $list_tree,
            'category' => $this->_category,
            'builders' => $this->load->view($this->template_dir . 'pages/kartoteka_organizations', array('organizations' => $builders, 'organization_type' => 'Застройщики'), TRUE),
            'sellers' => $this->load->view($this->template_dir . 'pages/kartoteka_organizations', array('organizations' => $sellers, 'organization_type' => 'Продавцы'), TRUE),
                ), TRUE);

        $this->render();
    }

    public function item($alias, $is_preview = FALSE) {
        $item = $this->Glossary_Model->search(array(
            'alias' => $alias,
            'file_category_id' => $this->_category['file_category_id'],
            'status' => $is_preview === TRUE ? FALSE : MY_Model::STATUS_ACTIVE,
                ), TRUE);

        if (!$item)
            show_404();

        $this->title = element('meta_title', $item, element('name', $item, ''));
        $this->meta_keywords = element('meta_keywords', $item, '');
        $this->meta_description = element('meta_description', $item, '');

        $this->load->library('Widget_gallery', array('this' => $this));
        // album create      
        $item['description'] = $this->widget_gallery->replace_gallery_marks($item['description']);

        $this->body = $this->load->view($this->template_dir . 'pages/kartoteka_item', array(
            'item' => $item,
//            'parent' => $this->Glossary_Model->get_by_primary_key($item['parent_id']),
                ), TRUE);

        $this->set_scripts_bottom('../jquery.jcarousel.min.js');
        $this->set_styles('../fancybox/source/jquery.fancybox.css');
        $this->set_scripts(array('../fancybox/source/jquery.fancybox.pack.js', '../fl/fl_gallery.js'));

        $this->breadcrumbs_add(element('name', $this->_category), '/' . $this->uri->segment(1))
                ->breadcrumbs_add(element('name', $item));

        $this->render();
    }

    public function organizations($alias) {

        $item = $this->Organizations_Model->search(array('alias' => $alias,), TRUE);
        if (!$item)
            show_404();

        $this->title = element('meta_title', $item, element('name', $item, ''));
        $this->meta_keywords = element('meta_keywords', $item, '');
        $this->meta_description = element('meta_description', $item, '');

        $this->load->library('Widget_gallery', array('this' => $this));
        // album create      
        $item['description'] = $this->widget_gallery->replace_gallery_marks($item['description']);

        $this->load->model('Object_Model');

        if ($this->input->is_ajax_request()) {
            $objects = $this->Object_Model->search(array(
                'organization_id' => element('organization_id', $item),
                'limit' => $limit = (int)$this->input->get('limit'),
                'offset' => $offset = (int)$this->input->get('offset'),
            ));

            $is_show_more_objects = ($tmp = $this->Object_Model->found_rows()) > $limit + $offset;

            echo json_encode(array(
                'success' => !empty($objects),
                'view' => $this->load->view($this->template_dir . 'pages/catalog_list', array('objects' => $objects, 'hide_list_wrapper' => TRUE), TRUE),
                'has_more' => $is_show_more_objects,
            ));
            return;
        }

        $objects = $this->Object_Model->search(array('organization_id' => element('organization_id', $item), 'limit' => 5, 'offset' => 0));
        $is_show_more_objects = $this->Object_Model->found_rows() > 5;
        
        $tags = $this->Organizations_Model->get_tags($item, FALSE);
        
        $this->load->model('Posts_Model');
        
        $read_more = !empty($tags) ? $this->Posts_Model->search(array(
                    'tag_id' => array_keys(simple_tree($tags, 'tag_id')),
                    'order' => 'created',
                    'order_direction' => 'DESC',
                    'limit' => 5,
                    'offset' => 0,
                    'not_post_id' => element('post_id', $item),
                )) : array();
        
        $read_more = !empty($read_more) ? $this->load->view($this->template_dir . 'widgets/read_more', array(
            'read_more' => $read_more,
            'categories' => simple_tree ($this->File_Categories->get_by_field('prefix', $this->Posts_Model->get_types(), FALSE), 'file_category_id'),
        ), TRUE) : '';

        $this->body = $this->load->view($this->template_dir . 'pages/kartoteka_item_organization', array(
            'item' => $item,
            'tags' => $tags,
            'objects' => $this->load->view($this->template_dir . 'pages/catalog_list', array('objects' => $objects), TRUE),
            'is_show_more_objects' => $is_show_more_objects,
            'read_more' => $read_more,
                ), TRUE);

        $this
                ->set_scripts_bottom(array('../jquery.jcarousel.min.js', 'kartoteka_organizations.js'))
                ->set_styles('../fancybox/source/jquery.fancybox.css')
                ->set_scripts(array('../fancybox/source/jquery.fancybox.pack.js', '../fl/fl_gallery.js'))
                ->breadcrumbs_add(element('name', $this->_category), '/' . $this->uri->segment(1))
                ->breadcrumbs_add(element('name', $item))
                ->render();
    }

}
