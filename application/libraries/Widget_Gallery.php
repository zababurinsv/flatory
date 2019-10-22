<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Widget' . EXT;

/**
 * Widget_gallery
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Widget_gallery extends Widget {

    /**
     * Regexp album mark
     * @var string 
     */
    private $album_mark_tpl = '/\{\{=it.album:\d+\}\}/';

    /**
     * Image on popup
     * @var string
     */
    private $path_full = '/images/1140x730/';

    /**
     * Image on page
     * @var string 
     */
    private $path_middle = '/images/570x380/';

    /**
     * Album preview
     * @var string 
     */
    private $path_small = '/images/255x170/';

    public function __construct($params) {
        parent::__construct($params);

        // set widget path
        $this->_view_path = $this->_view_path . 'gallery' . DIRECTORY_SEPARATOR;
        $this->load->model('Image_Albums');
    }

    /**
     * Create album marker
     * @param int $album_id
     * @return string
     */
    public function create_mark($album_id) {
        return '{{=it.album:' . (int) $album_id . '}}';
    }

    /**
     * Replace gallery marks ($this->album_mark_tpl)
     * @param string $text
     * @return string
     */
    public function replace_gallery_marks($text) {

        $albums = $this->_is_albume_include($text);

        if (empty($albums))
            return $text;

        foreach ($albums as $album) {
            $gallery = '';
            preg_match('/\d+/', $album, $id);
            $album_id = (int) element(0, $id, 0);

            if ($album_id) {
                $gallery = $this->render($album_id);
            }
            $text = str_replace($album, $gallery, $text);
        }

        return $text;
    }

    /**
     * Render gallery
     * @param int/array $album
     * @return string \View
     */
    public function render($album) {        
        if (!is_array($album))
            $album = $this->_CI->Image_Albums->get_with_images_by_id((int) $album);
        if (empty($album))
            return '';

        $this->_set_assets();

        return $this->load->view($this->_view_path . 'index', array(
                    'list' => $album,
                    'path_full' => $this->path_full,
                    'path_middle' => $this->path_middle,
                        ), TRUE);
    }

    /**
     * is albume include in string ($this->album_mark_tpl)
     * @param string $text
     * @return array
     */
    private function _is_albume_include($text) {
        preg_match_all($this->album_mark_tpl, $text, $matches);
        return element(0, $matches, array());
    }

    /**
     * Set required css & js
     */
    private function _set_assets() {
        $this->controller->set_styles('../fancybox/source/jquery.fancybox.css');
        $this->controller->set_scripts(array(
            '../jquery.jcarousel.min.js',
            '../fancybox/source/jquery.fancybox.pack.js',
        ));
        $this->controller->set_scripts_bottom(array(
            '../fl/fl_gallery.js',
        ));
    }

    /**
     * Render albums
     * @param array $albums
     * @return string
     */
    public function render_albums($albums) {
        
        $this->_set_assets();
                
        if (!$albums)
            return '';

        return $this->load->view($this->_view_path . 'albums', array(
                    'albums' => $albums,
                    'path_full' => $this->path_full,
                    'path_middle' => $this->path_middle,
                    'path_small' => $this->path_small,
                    'widget' => $this,
                        ), TRUE);
    }

}
