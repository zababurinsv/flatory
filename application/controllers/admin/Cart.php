<?php

class Cart extends MY_Controller {

    private $file_category = 'cart';

    /**
     * Model storage
     * @var Storage_Files 
     */
    public $Storage_Files;

    /**
     * Model categories
     * @var \File_Categories 
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['login_ok'])) {
            redirect('/admin/login');
        }

        $this->load->model('Storage_Files');
        $this->load->model('File_Categories');
        
        $this->set_breadcrumb('Каталог', '/admin/objects/');
    }

    public function index($object_id = "") {
        if ($object_id == '') {
            redirect('/admin/objects');
        }
        $this->title = "Карточка";
        $this->content = $this->get_nav($object = $this->db->where('id', $object_id)->get('main_object')->row_array());
        $file_category = $this->File_Categories->get_by_field('prefix', $this->file_category);
        
        if(isset($object['name']))
            $this->title = $object['name'];

        $this->set_breadcrumb($this->title);
        
        // get images
        $images = simple_tree($this->Storage_Files->get_by_category(element('file_category_id', $file_category, 0), $object_id), 'sort');

        $schema = array(1 => '140x215', 2 => '286x215', 3 => '432x215');

        foreach ($schema as $key => $size)
            $content['images_simple_upload'][] = $this->load->view($this->template_dir . 'widgets/image_simple_upload', array(
                'image' => element($key, $images, array()),
                'attr' => array('image_class' => 'cart_' . $size, 'class' => 'cart_image_uploader'),
                'title' => 'Формат (' . $size . ')px',
                'input_name' => 'file_' . $key,
                'upload_place_content' => $this->load->view($this->template_dir . 'objects/cart_form_component', array('data' => element($key, $images, array()), 'index' => $key), TRUE),
                'filters' => json_encode(array()),
                    ), TRUE);

        $content['id'] = (int) $object_id;

        $this->content .= $this->load->view($this->template_dir . 'objects/cart', $content, TRUE);
        $this->load->library('Widget_storage', array('this' => $this, 'category' => element('file_category_id', $file_category, 0)));
        $this->content .= $this->widget_storage->render('popup', array('is_mass_edit' => FALSE)); // 'is_filter' => FALSE, 

        $this->set_scripts_bottom('object_cart.js');

        $this->render();
    }

    public function save_carts() {
        $object_id = (int) $this->input->post('id');
        if (!$object_id)
            redirect('admin/cart/');

        $object = $this->db->where('id', $object_id)->get('main_object')->row_array();
        if (empty($object))
            redirect('admin/cart/');

        $file_category = $this->File_Categories->get_by_field('prefix', $this->file_category);

        // set new images
        $post = $this->input->post();
        $files = $sorts = array();
        for ($i = 1; $i < 4; $i++) {
            if (( $file_id = element('file_' . $i, $post, 0))) {
                $files[] = $file_id;
                $sorts[$file_id] = $i;
                // update alt 
                $this->Storage_Files->update_by_primary_key($file_id, array('alt' => element('text_' . $i, $post, 0)));
            }
        }
        if(!empty($files))
            $this->Storage_Files->set_files_involves(element('file_category_id', $file_category, 0), $object_id, element('alias', $object, ''), $files, $sorts);
        
        $this->db->where('id', $object_id)->update('main_object', ['updated' => date('Y-m-d H:i:s')]);

        redirect('admin/cart/' . $object_id);
    }

    /**
     * Get navs fo current Controller
     * @param array $object
     * @return string
     */
    private function get_nav($object = array()) {
        $nav = array(
            array('name' => 'Описание', 'path' => '/admin/objects/general_info/'),
            array('name' => 'Местонахождение', 'path' => '/admin/objects/object_location/'),
            array('name' => 'Tex. характеристики', 'path' => '/admin/objects/technical_characteristics/'),
            array('name' => 'Стоимость', 'path' => '/admin/objects/cost/'),
            array('name' => 'Планировки', 'path' => '/admin/objects/plan/'),
            array('name' => 'Фото строительства', 'path' => '/admin/objects/gallery/'),
            array('name' => 'Видео', 'path' => '/admin/objects/video/'),
            array('name' => 'Документация', 'path' => '/admin/objects/documents/'),
            array('name' => 'Инфраструктура', 'path' => '/admin/objects/infrastructure/'),
            array('name' => 'Застройщики', 'path' => '/admin/objects/builders/'),
            array('name' => 'Продавцы', 'path' => '/admin/objects/sellers/'),
            array('name' => 'Карточка', 'path' => '/admin/cart/'),
            array('name' => 'Мета-теги', 'path' => '/admin/objects/seo/'),
            array('name' => 'Панорамы', 'path' => '/admin/objects/panorama/'),
            array('name' => 'Публикация', 'path' => '/admin/objects/publish/'),
        );

        return $this->load->view($this->template_dir . 'navs/tabs_object', array('list' => $nav, 'object' => $object), TRUE);
    }

}
