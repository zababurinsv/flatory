<?php
//
//if (PHP_SAPI !== 'cli')
//    exit('No web access allowed');

/**
 * Restore image in Storage files
 *
 * @date 18.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Image extends CI_Controller {

    private $origin_path;
    private $docs_path;

    /**
     * Model
     * @var \File_Categories 
     */
    public $File_Categories;

    public function __construct() {
        parent::__construct();

        $this->origin_path = DOCROOT . 'images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR;
        $this->docs_path = DOCROOT . 'docs' . DIRECTORY_SEPARATOR;

        $this->load->model('File_Types');
        $this->load->model('Storage_Files');
        $this->load->model('Proportions');
        $this->load->model('Image_Albums');
        $this->load->library('image_lib');
        $this->load->library('log');
        $this->load->model('File_Categories');
    }

    /**
     * restore category Catalog
     */
    public function catalog() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'catalog');
        $category_files = element('file_category_id', $file_category);

        // broken images
        $broken_images = $this->db->query("
                SELECT im.*, 
                lower(RIGHT(image,3)) as ext, 
                SUBSTRING(image,28,LENGTH(image) - 28 - 2)  as file_name,
                SUBSTRING(SUBSTRING(image,28,LENGTH(image) - 28 - 2), 33 , LENGTH(SUBSTRING(image,28,LENGTH(image) - 28 - 2))) as original_name,
                alias
                FROM cat_images_object  as im
                left join cat_main_object as o on im.id_object = o.id
                where  image not like '%.jpg' and image not like '%.png'
                and im.status = 0
            ")->result_array();

        $this->_restore($category_files, $broken_images);

        // normal images (ext not broken)
        $normal_images = $this->db->query("
                SELECT im.*, 
                lower(RIGHT(image,3)) as ext, 
                SUBSTRING(image,28,LENGTH(image) - 28 - 3)  as file_name,
                SUBSTRING(SUBSTRING(image,28,LENGTH(image) - 28 - 3), 33 , LENGTH(SUBSTRING(image,28,LENGTH(image) - 28 - 3))) as original_name,
                alias
                FROM cat_images_object  as im
                left join cat_main_object as o on im.id_object = o.id
                where  (image like '%.jpg' or image like '%.png')
                and im.status = 0
            ")->result_array();

        $this->_restore($category_files, $normal_images);
    }

    /**
     * Restore pluns
     */
    public function pluns() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'plans');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore albums Pluns', __CLASS__);

        // broken images
        $broken_images = $this->db->query("
                SELECT object_id, alb.name as alubm_name, im.*, img as image, 
                lower(RIGHT(img,3)) as ext, 
                SUBSTRING(img,14,LENGTH(img) - 14 - 2)  as file_name,
                SUBSTRING(SUBSTRING(img,14,LENGTH(img) - 14 - 2), 33 , LENGTH(SUBSTRING(img,14,LENGTH(img) - 14 - 2))) as original_name,
                alias
                FROM cat_scrin_min  as im
                left join cat_pluns as alb on im.album_id = alb.id
                left join cat_main_object as o on alb.object_id = o.id
                where  img not like '%.jpg' and img not like '%.png' and img not like '%.gif'
            ")->result_array();

        $this->_restore_album($category_files, $broken_images);
        $this->log->write_cli_log('Count broken images: ' . count($broken_images), __CLASS__);

        // normal images (ext not broken)
        $normal_images = $this->db->query("
                SELECT object_id, alb.name as alubm_name, im.*, img as image, 
                lower(RIGHT(img,3)) as ext, 
                SUBSTRING(img,14,LENGTH(img) - 14 - 3)  as file_name,
                SUBSTRING(SUBSTRING(img,14,LENGTH(img) - 14 - 3), 33 , LENGTH(SUBSTRING(img,14,LENGTH(img) - 14 - 3))) as original_name,
                alias
                FROM cat_scrin_min  as im
                left join cat_pluns as alb on im.album_id = alb.id
                left join cat_main_object as o on alb.object_id = o.id
                where (img like '%.jpg' or img like '%.png' or img like '%.gif')
            ")->result_array();

        $this->log->write_cli_log('Count normal images: ' . count($normal_images), __CLASS__);
        $this->_restore_album($category_files, $normal_images);
        $this->log->write_cli_log('END restore albums Pluns', __CLASS__);
    }

    /**
     * Restore photo construction
     */
    public function photo_construction() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'photo_construction');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore albums Photo Construction', __CLASS__);

        // broken images
        $broken_images = $this->db->query("
                SELECT object_id, alias, alb.name as alubm_name, im.*, img as image, 
                lower(RIGHT(img,3)) as ext, 
                SUBSTRING(img,17,LENGTH(img) - 17 - 2)  as file_name,
                SUBSTRING(SUBSTRING(img,17,LENGTH(img) - 17 - 2), 33 , LENGTH(SUBSTRING(img,17,LENGTH(img) - 17 - 2))) as original_name
                FROM cat_scrin_full  as im
                left join cat_albums as alb on im.album_id = alb.id
                left join cat_main_object as o on alb.object_id = o.id
                where  img not like '%.jpg' and img not like '%.png' and img not like '%.gif'
            ")->result_array();

        $this->_restore_album($category_files, $broken_images);
        $this->log->write_cli_log('Count broken images: ' . count($broken_images), __CLASS__);

        // normal images (ext not broken)
        $normal_images = $this->db->query("
                SELECT object_id, alias, alb.name as alubm_name, im.*, img as image, 
                lower(RIGHT(img,3)) as ext, 
                SUBSTRING(img,17,LENGTH(img) - 17 - 3)  as file_name,
                SUBSTRING(SUBSTRING(img,17,LENGTH(img) - 17 - 3), 33 , LENGTH(SUBSTRING(img,17,LENGTH(img) - 17 - 3))) as original_name
                FROM cat_scrin_full  as im
                left join cat_albums as alb on im.album_id = alb.id
                left join cat_main_object as o on alb.object_id = o.id
                where  (img like '%.jpg' or img like '%.png' or img like '%.gif')
            ")->result_array();

        $this->log->write_cli_log('Count normal images: ' . count($normal_images), __CLASS__);
        $this->_restore_album($category_files, $normal_images);
        $this->log->write_cli_log('END restore albums Photo Construction', __CLASS__);
    }

    /**
     * Restore object images
     * @param int $category_files
     * @param array $restore_files
     */
    private function _restore($category_files, $restore_files, $involve_sort = FALSE) {
        $this->log->write_cli_log(__FUNCTION__ . ' category: ' . $category_files . ' restore count: ' . count($restore_files), __CLASS__);

        $proportions = $this->Proportions->get_category_proportions($category_files);
        $this->log->write_cli_log('Count proportions for current category: ' . count($proportions), __CLASS__);

        foreach ($restore_files as $image) {
            $file = DOCROOT . implode(DIRECTORY_SEPARATOR, explode('/', substr($image['image'], 1)));

            // fole format 
            $file_format = $this->db->select('*')->from('file_formats')->where('ext', $image['ext'])->get()->row_array();
            $file_format_id = (int) element('file_format_id', $file_format, 0);
            // skip if file not found or file format undefined
            if (!$file_format_id || !file_exists($file)) {
                $this->log->write_cli_log('ERROR FILE FORMAT: ' . $image['image'], __CLASS__);
                continue;
            }


            $name = element('file_name', $image, '');

            $isset_file_name = $this->Storage_Files->get_with_type($name, 'name');
//            vdump($isset_file_name);

            if (!empty($isset_file_name))
                $name = md5(time()) . '_' . $name;

            $image_size = ($is_image = ((int) element('file_type_id', $file_format) === 1)) ? getimagesize($file) : array();

            //  add file in file storage
            $new = array(
                'name' => $name,
                'original_name' => element('original_name', $image, ''),
                'size' => filesize($file),
                'x' => element(0, $image_size, 0),
                'y' => element(1, $image_size, 0),
                'user_id' => 0, // @todo Auth !!!
                'file_format_id' => $file_format_id,
                'created' => date('Y-m-d H:i:s', now()),
                'alt' => element('comment', $image, ''),
            );
//
            $new_id = $this->Storage_Files->insert($new);

            $path = $is_image ? $this->origin_path : $this->docs_path;

            // copy file
            copy($file, $path . $name . '.' . $image['ext']);
            // create thumb
            if ($is_image) {
                $this->image_lib->thumb($name . '.' . $image['ext']);
                set_time_limit(999999999);
                // mass edit files (create images proportions)
                $this->Storage_Files->update_files(array($new_id), array('proportions' => $proportions));
                set_time_limit(999999999);
            }

            // set file involves
            $row = array(
                'file_category_id' => (int) $category_files,
                'file_id' => (int) $new_id,
                'parent_id' => (int) $image['id_object'],
                'parent_alias' => $image['alias'],
            );
            if ($involve_sort)
                $row['sort'] = element($involve_sort, $image, 0);
            $this->db->insert('file_involves', $row);
        }
    }

    /**
     * Restore albums
     * @param int $album_id
     * @param int $category_files
     * @param array $restore_files - images for current album
     * @return boolean
     */
    private function _restore_album($category_files, $restore_files) {
        $this->log->write_cli_log(__FUNCTION__ . ' category: ' . $category_files . ' restore count: ' . count($restore_files), __CLASS__);

        if (empty($restore_files) || !isset($restore_files[0]['album_id'])) {
            $this->log->write_cli_log('Albums not found!', __CLASS__, 'error');
            return FALSE;
        }
        // create tree by album id
        $albums = simple_tree_group($restore_files, 'album_id');

        foreach ($albums as $album) {
            if (empty($album) || !isset($album[0]['alubm_name'])) {
                $this->log->write_cli_log('Empty album!', __CLASS__, 'error');
                continue;
            }

            // create album
            $image_album_id = $this->Image_Albums->insert(array(
                'name' => $album[0]['alubm_name'],
                'object_id' => (int) $album[0]['object_id'],
                'file_category_id' => $category_files,
                'description' => '',
            ));
            if ($image_album_id) {
                $this->log->write_cli_log('Add album: ' . $image_album_id, __CLASS__);
                $this->_restore_album_data($image_album_id, $category_files, $album);
            }
        }
    }

    /**
     * Restore album data
     * @param int $category_files
     * @param array $restore_files
     */
    private function _restore_album_data($album_id, $category_files, $restore_files) {
        $this->log->write_cli_log(__FUNCTION__ .
                ' album_id: ' . $album_id .
                ' category: ' . $category_files .
                ' restore count: ' . count($restore_files), __CLASS__);

        $proportions = $this->Proportions->get_category_proportions($category_files);
        $this->log->write_cli_log('Count proportions for current category: ' . count($proportions), __CLASS__);

        $album_images = array();

        foreach ($restore_files as $image) {
            $file = DOCROOT . implode(DIRECTORY_SEPARATOR, explode('/', substr($image['image'], 1)));

            // fole format 
            $file_format = $this->db->select('*')->from('file_formats')->where('ext', $image['ext'])->get()->row_array();
            $file_format_id = (int) element('file_format_id', $file_format, 0);
            // skip if file not found or file format undefined
            if (!$file_format_id || !file_exists($file)) {
                $this->log->write_cli_log('File not found: ' . $file, __CLASS__, 'error');
                continue;
            }


            $name = element('file_name', $image, '');

            $isset_file_name = $this->Storage_Files->get_with_type($name, 'name');
//            vdump($isset_file_name);

            if (!empty($isset_file_name)) {
                $old_name = $name;
                $name = md5(time()) . '_' . $name;
                $this->log->write_cli_log('Rename file: ' . $old_name . ' --> ' . $name, __CLASS__, 'warning');
            }

            $image_size = getimagesize($file);

            //  add file in file storage
            $new = array(
                'name' => $name,
                'original_name' => element('original_name', $image, ''),
                'size' => filesize($file),
                'x' => element(0, $image_size, 0),
                'y' => element(1, $image_size, 0),
                'user_id' => 0, // @todo Auth !!!
                'file_format_id' => $file_format_id,
                'created' => date('Y-m-d H:i:s', now()),
                'alt' => element('comment', $image, ''),
            );
//
            $new_id = $this->Storage_Files->insert($new);
            $this->log->write_cli_log('Add file: ' . $new_id, __CLASS__);

            // copy file
            copy($file, $this->origin_path . $name . '.' . $image['ext']);
            $this->log->write_cli_log('Copy file: ' . $file . ' --> ' . $this->origin_path . $name . '.' . $image['ext'], __CLASS__);
            // create thumb
            $this->image_lib->thumb($name . '.' . $image['ext']);
            $this->log->write_cli_log('Create thumb: ' . $name . '.' . $image['ext'], __CLASS__);
            set_time_limit(999999999);
            $this->log->write_cli_log('Start create proportion', __CLASS__);
            // mass edit files (create images proportions)
            $this->Storage_Files->update_files(array($new_id), array('proportions' => $proportions));
            set_time_limit(999999999);
            $this->log->write_cli_log('End create proportion', __CLASS__);
            $album_images[] = array(
                'file_id' => $new_id,
                // set file involves
                'file_involve_id' => $this->Image_Albums->insert(array(
                    'file_category_id' => (int) $category_files,
                    'file_id' => (int) $new_id,
                    'parent_id' => (int) element('object_id', $image, 0),
                    'parent_alias' => element('alias', $image, ''),
                        ), 'file_involves')
            );
        }
        $this->log->write_cli_log('Count album (' . $album_id . ') image: ' . count($album_images), __CLASS__);
        // set relations files >---< albums
        $this->Image_Albums->update_album_images($album_id, $album_images);
    }

    public function delete_zero() {
        $this->Storage_Files->delete_marked_for_deletion();
    }

    /**
     * Restore carts
     */
    public function carts() {
        /**
          -- set SQL_SAFE_UPDATES = 0;
          -- update cat_storage_files as f left join cat_file_involves using(file_id)
          -- set f.status = 0
          -- where file_category_id = 10;
         */
        $file_category = $this->File_Categories->get_by_field('prefix', 'cart');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore ' . __FUNCTION__, __CLASS__);

        // broken images
        $broken_images = $this->db->query("
                SELECT 
                    id_object as object_id, 
                    lower(RIGHT(image,3)) as ext,
                    SUBSTRING(image,28,LENGTH(image) - 28 - 2)  as file_name,
                    SUBSTRING(SUBSTRING(image,28,LENGTH(image) - 28 - 2), 33 , LENGTH(SUBSTRING(image,28,LENGTH(image) - 28 - 2))) as original_name,
                    o.alias, i.*, i.`text` as `comment`
                    FROM cat_images_object as i
                    left join cat_main_object as o on id_object = o.id
                    where i.status != 0 
                    and o.status = 1
                    and image not like '%.jpg' and image not like '%.png' and image not like '%.gif'
            ")->result_array();

        $this->_restore($category_files, $broken_images, 'status');
        $this->log->write_cli_log('Count broken images: ' . count($broken_images), __CLASS__);

        // normal images (ext not broken)
        $normal_images = $this->db->query("
                SELECT 
                    id_object as object_id, 
                    lower(RIGHT(image,3)) as ext,
                    SUBSTRING(image,28,LENGTH(image) - 28 - 3)  as file_name,
                    SUBSTRING(SUBSTRING(image,28,LENGTH(image) - 28 - 3), 33 , LENGTH(SUBSTRING(image,28,LENGTH(image) - 28 - 3))) as original_name,
                    o.alias, i.*, i.`text` as `comment`
                    FROM cat_images_object as i
                    left join cat_main_object as o on id_object = o.id
                    where i.status != 0 
                    and o.status = 1
                    and (image like '%.jpg' or image like '%.png' or image like '%.gif')
            ")->result_array();

        $this->log->write_cli_log('Count normal images: ' . count($normal_images), __CLASS__);
        $this->_restore($category_files, $normal_images, 'status');
        $this->log->write_cli_log('END restore ' . __FUNCTION__, __CLASS__);
    }

    public function builders() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'builders');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore ' . __FUNCTION__, __CLASS__);

        // broken images
        $broken_images = $this->db->query("
                SELECT 
                    id as id_object, 
                    lower(RIGHT(logo,3)) as ext,
                    SUBSTRING(logo,18,LENGTH(logo) - 18 - 2)  as file_name,
                    SUBSTRING(SUBSTRING(logo,18,LENGTH(logo) - 18 - 2), 33 , LENGTH(SUBSTRING(logo,18,LENGTH(logo) - 18 - 2))) as original_name,
                    '' as alias, i.*, i.company_name as comment, logo as image
                    FROM cat_builders_object as i
                    where logo not like '%.jpg' and logo not like '%.png' and logo not like '%.gif'
                    and logo != ''
            ")->result_array();

        $this->_restore($category_files, $broken_images, 'status');
        $this->log->write_cli_log('Count broken images: ' . count($broken_images), __CLASS__);

        // normal images (ext not broken)
        $normal_images = $this->db->query("
                SELECT 
                    id as id_object, 
                    lower(RIGHT(logo,3)) as ext,
                    SUBSTRING(logo,17,LENGTH(logo) - 17 - 3)  as file_name,
                    SUBSTRING(SUBSTRING(logo,17,LENGTH(logo) - 17 - 3), 33 , LENGTH(SUBSTRING(logo,17,LENGTH(logo) - 17 - 3))) as original_name,
                    '' as alias, i.*, i.company_name as comment, logo as image
                    FROM cat_builders_object as i
                    where (logo like '%.jpg' or logo like '%.png' or logo like '%.gif')
                    and logo != ''
            ")->result_array();

        $this->log->write_cli_log('Count normal images: ' . count($normal_images), __CLASS__);
        $this->_restore($category_files, $normal_images, 'status');
        $this->log->write_cli_log('END restore ' . __FUNCTION__, __CLASS__);
    }

    /**
     * Restore docs
     */
    public function docs() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'docs');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore ' . __FUNCTION__, __CLASS__);

        $queries = array(
            "broken" => "SELECT 
                object_id as id_object, 
                lower(RIGHT(file,3)) as ext,
                SUBSTRING(file,19,LENGTH(file) - 19 - 2)  as file_name,
                SUBSTRING(SUBSTRING(file,19,LENGTH(file) - 19 - 2), 33 , LENGTH(SUBSTRING(file,19,LENGTH(file) - 19 - 2))) as original_name,
                o.alias, i.*, i.name as `comment`, file as image
                FROM cat_files as i
                left join cat_main_object as o on object_id = o.id
                where 
                file not like '%.jpg' and file not like '%.png' and file not like '%.gif'
                and file not like '%.pdf' and file not like '%.doc' and file not like '%.docx' and file not like '%.ini'
                and file not like '%.xls' 
                and lower(RIGHT(file,3)) != 'ocx'",
            "normal" => "SELECT 
                object_id as id_object, 
                lower(RIGHT(file,3)) as ext,
                SUBSTRING(file,19,LENGTH(file) - 19 - 3)  as file_name,
                SUBSTRING(SUBSTRING(file,19,LENGTH(file) - 19 - 3), 33 , LENGTH(SUBSTRING(file,19,LENGTH(file) - 19 - 3))) as original_name,
                o.alias, i.*, i.name as `comment`, file as image
                FROM cat_files as i
                left join cat_main_object as o on object_id = o.id
                where 
                (file like '%.jpg' or file like '%.png' or file like '%.gif'
                or file like '%.pdf' or file like '%.doc' or file like '%.ini'
                or file like '%.xls') 
                and lower(RIGHT(file,3)) != 'ocx'",
            "broken_docx" => "SELECT 
                object_id as id_object, 
                lower(RIGHT(file,4)) as ext,
                SUBSTRING(file,19,LENGTH(file) - 19 - 3)  as file_name,
                SUBSTRING(SUBSTRING(file,19,LENGTH(file) - 19 - 3), 33 , LENGTH(SUBSTRING(file,19,LENGTH(file) - 19 - 3))) as original_name,
                o.alias, i.*, i.name as `comment`, file as image
                FROM cat_files as i
                left join cat_main_object as o on object_id = o.id
                where 
                file not like '%.docx'
                and lower(RIGHT(file,3)) = 'ocx'",
            "docx" => "SELECT 
                object_id as id_object, 
                lower(RIGHT(file,4)) as ext,
                SUBSTRING(file,19,LENGTH(file) - 19 - 3)  as file_name,
                SUBSTRING(SUBSTRING(file,19,LENGTH(file) - 19 - 3), 33 , LENGTH(SUBSTRING(file,19,LENGTH(file) - 19 - 3))) as original_name,
                o.alias, i.*, i.name as `comment`, file as image
                FROM cat_files as i
                left join cat_main_object as o on object_id = o.id
                where 
                file like '%.docx'
                and lower(RIGHT(file,3)) = 'ocx'",
        );

        foreach ($queries as $key => $sql) {
            $list = $this->db->query($sql)->result_array();
//            vdump($list);
            $this->_restore($category_files, $list);
            $this->log->write_cli_log('Count broken images: ' . count($list), __CLASS__);
        }
    }

    public function news_anons() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'news');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore ' . __FUNCTION__, __CLASS__);

        // only broken
        $sql = "SELECT 
            id as id_object,
            lower(RIGHT(image,3)) as ext,
            SUBSTRING(image,14,LENGTH(image) - 14 - 2)  as file_name,
            SUBSTRING(SUBSTRING(image,14,LENGTH(image) - 14 - 2), 33 , LENGTH(SUBSTRING(image,14,LENGTH(image) - 14 - 2))) as original_name,
            alias, i.name as `comment`, image as image, i.* 
            FROM cat_news as i
            where image not like '%.jpg' and image not like '%.png' and image not like '%.gif'
            and image != '' and image is not null";

        $list = $this->db->query($sql)->result_array();
                
        $this->_restore_anons($category_files, $list, 'news');
        $this->log->write_cli_log('Count broken images: ' . count($list), __CLASS__);
    }
    
    public function articles_anons() {
        $file_category = $this->File_Categories->get_by_field('prefix', 'articles');
        $category_files = element('file_category_id', $file_category);

        $this->log->write_cli_log('Start restore ' . __FUNCTION__, __CLASS__);
        
        // only broken
        $sql = "SELECT 
            id as id_object,
            lower(RIGHT(image,3)) as ext,
            SUBSTRING(image,17,LENGTH(image) - 17 - 2)  as file_name,
            SUBSTRING(SUBSTRING(image,17,LENGTH(image) - 17 - 2), 33 , LENGTH(SUBSTRING(image,17,LENGTH(image) - 17 - 2))) as original_name,
            alias, i.name as `comment`, image as image, i.* 
            FROM cat_article as i
            where image not like '%.jpg' and image not like '%.png' and image not like '%.gif'
            and image != '' and image is not null";

        $list = $this->db->query($sql)->result_array();
        
//        vdump($category_files);
        
        $this->_restore_anons($category_files, $list, 'article');
        $this->log->write_cli_log('Count broken images: ' . count($list), __CLASS__);
    }

    private function _restore_anons($category_files, $restore_files, $resource_table) {
        $this->log->write_cli_log(__FUNCTION__ . ' category: ' . $category_files . ' restore count: ' . count($restore_files), __CLASS__);

        foreach ($restore_files as $image) {
            $file = DOCROOT . implode(DIRECTORY_SEPARATOR, explode('/', substr($image['image'], 1)));

            // fole format 
            $file_format = $this->db->select('*')->from('file_formats')->where('ext', $image['ext'])->get()->row_array();
            $file_format_id = (int) element('file_format_id', $file_format, 0);
            // skip if file not found or file format undefined
            if (!$file_format_id || !file_exists($file)) {
                $this->log->write_cli_log('ERROR FILE FORMAT: ' . $image['image'], __CLASS__);
                continue;
            }


            $name = element('file_name', $image, '');

            $isset_file_name = $this->Storage_Files->get_with_type($name, 'name');
//            vdump($isset_file_name);

            if (!empty($isset_file_name))
                $name = md5(time()) . '_' . $name;

            $image_size = ($is_image = ((int) element('file_type_id', $file_format) === 1)) ? getimagesize($file) : array();

            //  add file in file storage
            $new = array(
                'name' => $name,
                'original_name' => element('original_name', $image, ''),
                'size' => filesize($file),
                'x' => element(0, $image_size, 0),
                'y' => element(1, $image_size, 0),
                'user_id' => 0, // @todo Auth !!!
                'file_format_id' => $file_format_id,
                'created' => date('Y-m-d H:i:s', now()),
                'alt' => element('comment', $image, ''),
            );
//
            $new_id = $this->Storage_Files->insert($new);

            $path = $is_image ? $this->origin_path : $this->docs_path;

            // copy file
            copy($file, $path . $name . '.' . $image['ext']);
            
            $this->image_lib->thumb($name . '.' . $image['ext']);

            // set file involves
            $row = array(
                'file_category_id' => (int) $category_files,
                'file_id' => (int) $new_id,
                'parent_id' => (int) $image['id_object'],
                'parent_alias' => $image['alias'],
            );
            
            $this->db->insert('file_involves', $row);
            
            // save in resource table
            $this->db->where('id', $image['id_object']);
            $update_resource = $this->db->update($resource_table, array('file_id' => (int) $new_id));
            if(!$update_resource)
                $this->log->write_cli_log('ERROR FILE FORMAT: ' . $image['image'], __CLASS__);
            
            
        }
    }

}
