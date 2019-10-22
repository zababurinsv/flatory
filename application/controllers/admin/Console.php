<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Console extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->content = $this->load->view($this->template_dir .'pages/Console', array(), TRUE);

        $this->render();
    }

    public function exit_admin() {
        unset($_SESSION['login_ok']);
        redirect('/admin/login');
    }

}
