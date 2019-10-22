<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model file_categories
 *
 * @date 22.02.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class File_Categories extends MY_Model{
    
    protected $table = 'file_categories';
    protected $primary_key = 'file_category_id';
    
    public function __construct() {
        parent::__construct();
    }
}
