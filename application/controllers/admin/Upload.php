<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Upload Controller
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Upload extends MY_Controller {

    private $path = '/admin/upload/';
    private $uploaddir = 'uploads';

    public function __construct() {
        parent::__construct();

        $this->load->model('File_Types');
        $this->load->model('Storage_Files');
        $this->load->model('Tags_Model');
        $this->load->library('image_lib');
    }

    /**
     * Action upload file
     */
    public function index() {
        $this->title = 'Зазгрузка файлов';

//        $tags = $this->Tags_Model->get_tags(FALSE);
//        // widget mass edit
//        $widget = $this->load->view($this->template_dir . 'widgets/mass_edit_image', array(
//            'proportions' => $this->Storage_Files->get_proportions(),
//            'tags' => defined('JSON_UNESCAPED_UNICODE') ? json_encode($tags, JSON_UNESCAPED_UNICODE) : json_encode_unescaped_unicode($tags),
//        ), TRUE);
        
        $this->load->library('Widget_storage', array('this' => $this));
        
        $this->content = $this->load->view($this->template_dir . 'upload_file', array(
            'nav' => $this->_nav_file_types(),
            'mass_edit' => $this->widget_storage->mass_edit(),
        ), TRUE);
        
//        $this->styles[] = 'jquery-ui.min.css';
//        $this->styles[] = 'jquery.tagit.css';
//        $this->scripts = array(
//            'jquery-1.10.2.min.js',
//            'functions.js',
//            '/front/forms.js',
//            'jquery-ui-1.9.2.min.js',
//            'tag-it.min.js',
//        );
//        
//        $this->bottom_scripts[] = 'doT.min.js';
//        $this->bottom_scripts[] = 'fileuploader.js';
//        $this->bottom_scripts[] = 'upload.js';
//        $this->bottom_scripts[] = 'widget_mass_edit_image.js';
        $this
                ->set_scripts_bottom(['/js/fileuploader.js', '/js/upload.js', '/js/widget_mass_edit_image.js'])
                ->render();
    }

    /**
     * Ajax upload file
     */
    public function process() {
        $uploaddir =  DOCROOT . $this->uploaddir;
        $hash = element('HTTP_UPLOAD_ID', $_SERVER, '');

        $file = $this->Storage_Files->get_with_type($hash, 'name');

        // abort upload
        if (element('action', $_GET, '') === "abort") {
            if (is_file($uploaddir . "/" . $hash . ".html5upload"))
                unlink($uploaddir . "/" . $hash . ".html5upload");
            print "ok abort";
            return;
        }

        // success upload
        if (element('action', $_GET, '') === "done") {
            log_message('info', "Finished for hash " . $hash);

            if (is_file($uploaddir . "/" . $hash . ".original"))
                unlink($uploaddir . "/" . $hash . ".original");

            rename($uploaddir . "/" . $hash . ".html5upload", $uploaddir . "/" . $hash . ".original");

            $fw = fopen($uploaddir . "/" . $hash . ".original_ready", "wb");
            if ($fw) {
                fclose($fw);
                unlink($uploaddir . "/" . $hash . ".original_ready");
            }

            $is_move = $this->_move_file($file);
            if ($this->Storage_Files->is_image($file)) {
                $size = $this->image_lib->get_image_size(element('file_name', $file, ''));
                if (!empty($size)) {
                    $this->Storage_Files->update_by_primary_key(element('file_id', $file, ''), array(
                        'x' => element('width', $size, 0),
                        'y' => element('height', $size, 0),
                    ));
                }
            }

            echo json_encode(array('success' => $is_move, 'data' => $file));
            return;
        }

        // process upload
        if ($file) {

            $portion_from = (int) element('HTTP_PORTION_FROM', $_SERVER, 0);
            $portion_size = (int) element('HTTP_PORTION_SIZE', $_SERVER, 0);

            log_message('info', "Uploading chunk. Hash " . $hash . " (" . $portion_from . "-" . $portion_from + $portion_size . ", size: " . $portion_size . ")");

            $filename = $uploaddir . "/" . $hash . ".html5upload";

            if ($portion_from === 0)
                $fout = fopen($filename, "wb");
            else
                $fout = fopen($filename, "ab");

            if (!$fout) {
                log_message('error', "Can't open file for writing: " . $filename);
                header("HTTP/1.0 500 Internal Server Error");
                print "Can't open file for writing.";
                return;
            }

            $fin = fopen("php://input", "rb");
            if ($fin) {
                while (!feof($fin)) {
                    $data = fread($fin, 1024 * 1024);
                    fwrite($fout, $data);
                }
                fclose($fin);
            }

            fclose($fout);


            header("HTTP/1.0 200 OK");
            print "ok\n";
        } else {
            log_message('error', "Uploading chunk. Wrong hash " . $hash);
            header("HTTP/1.0 500 Internal Server Error");
            print "Wrong session hash.";
        }
    }

    /**
     * Ajax post - create file
     * @return json
     */
    public function create() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);

        $data = array('success' => FALSE, 'data' => array());

        $post = xss_clean($_POST);

        $format = $this->Storage_Files->get_file_format(element('type', $post, ''));
        if (empty($format) || !(int) element('file_format_id', $format, 0)) {
            $data['error'] = 'Формат "' . element('type', $post, '') . '" не поддерживается';
            $data['data'] = array('original_name' => htmlspecialchars(element('name', $post, '')));
            echo json_encode($data);
            return FALSE;
        }

        $new = array(
            'name' => md5(time() . element('name', $post, '')),
            'original_name' => htmlspecialchars(element('name', $post, '')),
            'size' => (int) element('size', $post, 0),
            'user_id' => 0, // @todo Auth !!!
            'file_format_id' => $format['file_format_id'],
            'created' => date('Y-m-d H:i:s', now())
        );

        $new_id = $this->Storage_Files->insert($new);
        $file = $this->Storage_Files->get_by_primary_key($new_id);

        $data['success'] = !!$new_id;
        $data['data'] = $file;

        echo json_encode($data);
        return FALSE;
    }

    /**
     * Move file
     * @param array $file
     * @return bool
     */
    private function _move_file($file) {

        if (empty($file))
            return FALSE;

        $uploaded = $this->uploaddir . "/" . element('name', $file, '') . ".original";

        if (!is_file($uploaded))
            return FALSE;

        $path = '.' . element('path', $file, '/files/');
        $file_name = element('file_name', $file, '');
        $is_move = rename($uploaded, $path . $file_name);
        if ($is_move)
            $this->image_lib->thumb($file_name);
        return $is_move;
    }

    /**
     * Ajax post - delete uploaded file
     * @return json
     */
    public function delete() {
        if (!$this->input->is_ajax_request())
            redirect($this->path);
        $data = array('success' => FALSE, 'data' => array());

        // get & check file exists
        $file_id = (int) element('fid', $_POST, 0);
        $file = $this->Storage_Files->get_with_type($file_id);
        $file_path = DOCROOT . element('path', $file, '') . element('file_name', $file, '');
        if (empty($file) || !file_exists($file_path)) {
            $data['error'] = 'Файл не найден!';
            echo json_encode($data);
            return FALSE;
        }
        // check file involves    
        $file_involves = $this->Storage_Files->get_file_involves($file_id);
        if (!empty($file_involves)) {
            $data['data'] = $file_involves;
            $data['error'] = 'Невозможно удалить файл, так как он участвует в публикациях!';
            echo json_encode($data);
            return FALSE;
        }
        // delete file
        unlink($file_path);
        // delete thumb
        if ((int) element('file_type_id', $file, 0) === Storage_Files::FILE_IMAGE) {
            $this->config->load('image_lib');
            $thump_path = $this->config->item('thumb_folder') . element('file_name', $file, '');
            unlink($thump_path);
        }
        // delete row in db
        $this->Storage_Files->delete_by_primary_key($file_id);
        $data['success'] = TRUE;
        echo json_encode($data);
        return FALSE;
    }

    /**
     * Create navigation
     * @return type
     */
    private function _nav_file_types() {
        $types = $this->File_Types->get_list();

        $list = [];

        foreach ($types as $it) {
            $list[] = [
                'url' => '/admin/storage/'. $it['alias'],
                'title' => $it['name'],
                'active' => false,
            ];
        }

        $list[] = [
            'url' => '/admin/upload',
            'title' => 'Загрузить',
            'active' => TRUE,
        ];

        return $this->load->view($this->template_dir . 'navs/pills', array('list' => $list), TRUE);
    }
}
