<?php

if (PHP_SAPI !== 'cli')
    exit('No web access allowed');

class Cron extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('log');
    }
    
    /**
     * Обновление кол-ва объектов по всем справочникам
     */
    public function objects_counts() {
        $this->_line('Start ' . __FUNCTION__);
        $this->load->model('Geo');
        $this->Geo->handbks_object_counts_update();
        $this->_line('End ' . __FUNCTION__);
    }
    
    /**
     * print line
     * @param string $str
     */
    private function _line($str) {
        echo $str . PHP_EOL;
        $this->log->write_cli_log($str, __CLASS__);
    }
    
}
