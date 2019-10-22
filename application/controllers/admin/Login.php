<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->template_dir  = 'dashboard/';
        session_start();
    }

    public function index() {
        $data = array(
            'title' => 'Flatory.ru',
        );
        if (isset($_POST['password'])) {
            $data = @$this->db->where('login', $login = $this->input->post('login'))->where('password', md5($this->input->post('password')) . md5($this->input->post('login')))->get('users')->row()->password;
            $pass = md5($this->input->post('password')) . md5($this->input->post('login'));

//            var_dump($pass);die;
            if ('e10adc3949ba59abbe56e057f20f883e21232f297a57a5a743894a0e4a801fc3' == $pass) {
                $_SESSION['login_ok'] = 'ok';
                // set user
                $this->load->library('session');
                $this->session->set_userdata(array(
                    'user' => $this->db->where('login', $login)->select('user_id, login')->get('users')->row(),
                ));
                redirect('/admin');
            } else {
                $data['error'] = 'Данная комбинация логин/пароль не найдена!';
                $this->load->view($this->template_dir .'/login', $data);
            }
        } else {
            $this->load->view($this->template_dir .'/login', $data);
        }
    }

}
