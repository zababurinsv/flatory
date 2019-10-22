<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Image lib extend
 * 
 * @date 15.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Image_lib extends CI_Image_lib {
    
    /**
     * Name of config
     * @var string
     */
    protected $config_name = 'image_lib';

    /**
     * Separator for folder name (size)
     * @var string
     */
    protected $dest_folder_size_separator = '';
    
    /**
     * thumb settings
     * @var int 
     */
    protected $thumb_width = 0;
    protected $thumb_heigth = 0;
    /**
     * thumb settings
     * @var string 
     */
    protected $thumb_folder = '';

    public function __construct($props = array()) {
        parent::__construct($props);
    }
    
    /**
     * Get lib params
     * @param string $name
     * @return mixin
     */
    public function __get($name) {
        return isset($this->$name) ? $this->$name : NULL;
    }

    /**
     * Resize image
     * @param stirng $source_image - file name with ext (image.jpg)
     * @param int $width - px
     * @param int $height - px
     * @return type
     */
    public function resize($source_image, $width, $height, $dest_folder = FALSE) {
        // load lib config
        $config = load_config($this->config_name);
        
        $this->source_image = $source_image;
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->mime_type = TRUE;
        $dest_folder = !$dest_folder ? element('dest_folder', $config, $this->dest_folder) . $this->width
                . $this->dest_folder_size_separator . $this->height 
                . DIRECTORY_SEPARATOR : $dest_folder;
        $this->new_image = $dest_folder . $this->source_image;
        $this->error_msg = array();
        
        $this->initialize($config);
        return parent::resize();
    }

    /**
     * Create thumb
     * @param string $source_image - file name with ext (image.jpg)
     * @return type
     */
    public function thumb($source_image){
        return $this->resize($source_image, $this->thumb_width, $this->thumb_heigth, $this->thumb_folder);
    }
    
    /**
     * 
     * @param type $source_image
     */
    public function watermark($source_image){        
        $this->source_folder = '';
        $this->source_image = $this->full_src_path = $source_image;
        $this->new_image = '';
        $this->initialize();
        return parent::watermark();
    }
    
    /**
     * Get image size
     * @param string $source_image
     * @param string $source_folder
     * @return array
     */
    public function get_image_size($source_image, $source_folder = FALSE) {
        $source_folder = !$source_folder ? $this->source_folder : $source_folder;
        $size = getimagesize($source_folder . $source_image);
        $result = array();
        if(!empty($size)){
            $result['width'] = $size[0];
            $result['height'] = $size[1];
            $result['size_str'] = $size[3];
        }
        return $result;
    }
}
