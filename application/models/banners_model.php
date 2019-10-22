<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Banners_Model
 *
 * @date 03.10.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Banners_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * get all
     * @todo get from db
     * @param array $filters
     * @return array
     */
    public function get_all($filters = array()) {
        $banners = array(
            array(
                'position' => 'top',
                'content' => '<!-- /135692971/flatory-topline1 --><div id="div-gpt-ad-1477851160662-0" style="height:90px; width:970px; margin: 0 auto;"></div>',
                'status' => 1,
            ),
            array(
                'position' => 'middle',
                'content' => '<!-- /135692971/topline_podpoiskom_250 --><div id="div-gpt-ad-1481140745463-0" style="height:250px; width:1000px;"></div>',
                'status' => 1,
            ),
            array(
                'position' => 'left_top',
                'content' => '<!-- /135692971/flatory_240x400_left -->
                            <div id="div-gpt-ad-1477944838078-0" style="height:400px; width:240px;"></div>',
                'status' => 1,
            ),
            array(
                'position' => 'left',
                'content' => '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                <!-- left banner -->
                                <ins class="adsbygoogle"
                                     style="display:block"
                                     data-ad-client="ca-pub-1103342554250039"
                                     data-ad-slot="4333845501"
                                     data-ad-format="auto"></ins>
                                <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>',
                'status' => 1,
            ),
            array(
                'position' => 'right',
                'content' => '<div id="div-gpt-ad-1477947026674-0" style="height:600px; width:160px;"></div>',
                'status' => 1,
            ),
        );
        return $banners;
    }

}
