<?php
class Publicity extends MY_FrontController {

    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    
    public function index(){

//        @$data['login_control'] = $_SESSION['uid'];
        
        $this->title = 'Размещение рекламы на сайте о недвижимости Flatory.ru';
        $this->meta_description = 'Цены на размещение рекламы на портале Flatory.ru. Условия сотрудничества. Контакты рекламного отдела.';

        $this->body = $this->load->view( $this->template_dir .'pages/publicity', $data, TRUE);
        $this->render();
        
    }
}