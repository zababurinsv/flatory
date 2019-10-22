<?php

class Contacts extends MY_Controller {

    public function __construct() {
        parent::__construct();
        session_start();
        if (!isset($_SESSION['login_ok'])) {
            redirect('/admin/login');
        }
        $this->load->model('m_contacts', 'm_contacts');

        $this->set_breadcrumb('Настройки');
    }

    public function index() {
        $data = [
            'exicute' => $this->db->where('user_id', 1)->get('users')->row_array(),
            'title' => $this->title = 'Настройки',
            'breadcrumbs' => $this->get_breadcrumb(),
        ];

        $this->render($this->load->view($this->template_dir . 'pages/contacts', $data, TRUE));
    }

    public function contacts_edit() {
        $contacts_data = array(
            "org_name" => $this->input->post('org_name'),
            "adres" => $this->input->post('adres'),
            "e_mail" => $this->input->post('e_mail'),
            "phone1" => $this->input->post('phone1'),
            "phone2" => $this->input->post('phone2'),
            "details" => $this->input->post('details'),
        );
        $this->m_contacts->contacts_edit_db($contacts_data);
        redirect('/admin/contacts');
    }

    public function personal_infomation() {
        $personal_data = array(
            "name" => $this->input->post('name'),
            "login" => $this->input->post('login'),
            "password" => "",
            "e_mail_repeat" => $this->input->post('e_mail_repeat'),
            "e_mail_2" => $this->input->post('e_mail_2'),
            "phone3" => $this->input->post('phone3'),
        );
        if (($this->input->post('password') == $this->input->post('password_2')) && $this->input->post('password') != '') {
            $personal_data['password'] = md5($this->input->post('password')) . md5($personal_data['login']);
        } else {
            $personal_data['password'] = $this->db->where('user_id', 1)->select('password')->get('users')->row()->password;
        }
        $this->m_contacts->personal_infomation_db($personal_data);
        redirect('/admin/contacts');
    }

}
