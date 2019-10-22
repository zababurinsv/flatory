<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['image_library'] = 'gd2';

/**
 * source folder (for original image)
 */
$config['source_folder'] = DOCROOT.'images'. DIRECTORY_SEPARATOR .'original'. DIRECTORY_SEPARATOR;

/**
 * destination folder
 */
$config['dest_folder'] = DOCROOT.'images' .DIRECTORY_SEPARATOR;

/**
 * separator for folder name (size 150x100)
 */
$config['dest_folder_size_separator'] = 'x';

/**
 * thumb settings
 */
$config['thumb_width'] = 100;
$config['thumb_heigth'] = 100;
$config['thumb_folder'] = DOCROOT.'images'. DIRECTORY_SEPARATOR .'thumbs'. DIRECTORY_SEPARATOR;

/**
 * watermark
 */
$config['wm_type'] = 'overlay';
$config['wm_overlay_path'] = DOCROOT.'images'. DIRECTORY_SEPARATOR .'watermark_o70.png';
$config['wm_opacity'] = 50;
$config['wm_vrt_alignment'] = 'bottom'; 
$config['wm_hor_alignment'] = 'right';
$config['wm_hor_offset'] = 10;
$config['wm_vrt_offset'] = 10;
$config['wm_x_transp'] = 10;
$config['wm_y_transp'] = 1;

/**
 * uri path
 */
$config['uri_path_original'] = '/images/original/';
$config['uri_path_thumb'] = '/images/thumb/';