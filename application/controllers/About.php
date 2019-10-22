<?php
class About extends MY_FrontController {

    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index(){
        
//        @$data['login_control'] = $_SESSION['uid'];
        
        $this->title .= ' - Информация о проекте';
        $this->meta_description = 'Информация о сайте-каталоге недвижимости Flatory.ru. Задачи и миссия проекта. Контакты менеджмента сайта.';

        $this->body = $this->load->view( $this->template_dir .'pages/about', array(), TRUE);
        $this->render();
    }
}