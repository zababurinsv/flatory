<?php
class Tools extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->input->is_cli_request()
        or exit("Execute via command line: php index.php migrate");

        $this->load->library('migration');
    }

    public function index()
    {
        var_dump('test 0');
        if(!$this->migration->version(22))
        {
            var_dump('test 1');
            show_error($this->migration->error_string());
        }else{
            var_dump('test 2');
        }
//        if(!$this->migration->latest())
//        {
//            var_dump('test 1');
//            show_error($this->migration->error_string());
//        }else{
//            var_dump('test 2');
//        }
    }
    public function down()
    {
        if(!$this->migration->version(21))
        {
            var_dump('test 1');
            show_error($this->migration->error_string());
        }else{
            var_dump('test 2');
        }
    }
}