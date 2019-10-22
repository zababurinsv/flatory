<?php

/**
 * Admin Ajax controller
 * only ajax requests
 * @date 02.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Ajax extends MY_Controller {

    /**
     * Storage model
     * @var \Storage_Files 
     */
    public $Storage_Files;

    /**
     *
     * @var \Posts_Model 
     */
    public $Posts_Model;
    private $is_enable = false;

    public function __construct() {
        parent::__construct();
        // only ajax
        if (!$this->input->is_ajax_request())
            redirect('/');

        $this->load->model('Storage_Files');
        $this->load->model('Posts_Model');

        // check referrer - only from /admin/ section
        $this->load->library('user_agent');
        if (strpos($this->agent->referrer(), '/admin/') === FALSE) {
            echo json_encode(array('success' => FALSE));
            return FALSE;
        }
        $this->is_enable = TRUE;
    }

    /**
     * Delete album
     * @param int $album_id - image_album_id
     * @return boolean
     */
    public function delete_image_album($album_id) {
        if (!$this->is_enable)
            return FALSE;
        $this->load->model('Image_Albums');

        echo json_encode(array('success' => $this->Image_Albums->delete_by_primary_key($album_id)));
        return;
    }

    /**
     * Save sort albums
     * @return boolean
     */
    public function sort_albums() {
        if (!$this->is_enable)
            return FALSE;
        $this->load->model('Image_Albums');

        $result = array();
        $albums = $this->input->post('albums');
        foreach ($albums as $album) {
            if (isset($album['image_album_id']) && isset($album['sort'])) {
                $save = $this->Image_Albums->update_by_primary_key((int) $album['image_album_id'], array(
                    'sort' => (int) $album['sort']
                ));
                if ($save)
                    $result[] = $save;
            }
        }

        echo json_encode(array('success' => count($albums) === count($result)));
        return;
    }

    /**
     * Delete file & proportions
     */
    public function storage_delete() {

        $file_id = (int) $this->input->post('file_id');

        echo json_encode(array('success' => $this->Storage_Files->delete($file_id)));
    }

    /**
     * Response for ckeditor plugin flimage
     * @return null
     */
    public function flimage() {
        $post = xss_clean($_POST);

        $image = $this->Storage_Files->get_by_file_type('images', 'alias', $post);
        $data = array();

        $success = isset($image[0]);
        $error = !$success ? 'Изображение не связано с текущим объектом. Попробуйте сначала добавить его в альбом.' : '';
        if ($success) {
            $data['image'] = $image[0];
            $data['path_middle'] = '/images/570x380/';
            $data['path_full'] = '/images/1140x730/';
            $data['flimage'] = $this->load->view($this->template_dir . 'pages/flimage', $data, TRUE);
        }


        echo json_encode(array('success' => $success, 'data' => $data, 'error' => $error));
        return;
    }

    /**
     * handbk list
     * @param int $handbk_id
     * @return null
     */
    public function handbk($handbk_id) {
        // default model
        $this->load->model('Handbks_Model');
        $list = $this->Handbks_Model->get_related_handbk(array('handbk_id' => (int) $handbk_id, 'status' => MY_Model::STATUS_ACTIVE));
        echo json_encode(array('success' => !empty($list), 'data' => $list, 'handbk' => $this->Handbks_Model->search(['handbk_id' => (int)$handbk_id], TRUE)));
        return;
    }

    /**
     * Привязка раздела карточки объекта к объекту
     */
    public function object_sections() {

        $this->load->model('Object_Section_Model');

        $post = $this->input->post();

        $errors = [];

        if (!($object_id = (int) array_get($post, 'object_id')))
            $errors['object_id'] = 'Номер объекта не найден!';

        if (!$errors)
            $this->Object_Section_Model->set_object_sections($object_id, array_get($post, 'object_section_id'));

        echo json_encode([
            'success' => !$errors,
            'errors' => $errors,
            'input' => $post,
        ]);
    }

    /**
     * Get posts by name
     * @return boolean
     */
    public function post_names() {
        $success = FALSE;
        $data = array();
        $get = xss_clean($_GET);
        // получаем данные запроса
        $name = element('name', $get, FALSE);

        if ($name === FALSE) {
            echo json_encode(array('success' => $success, 'data' => $data));
            return FALSE;
        }
        $data = $this->Posts_Model->search(['name_like' => $name]);
        $decorate_data = array();
        foreach ($data as $item) {
            $decorate_data[] = array(
                'label' => $item['name'],
                'value' => $item['post_id'],
            );
        }
        $success = !empty($decorate_data);
        echo json_encode(array('success' => $success, 'data' => $decorate_data));
        return FALSE;
    }

    /**
     * Удаление  - фактически замена статуса на на MY_Model::STATUS_DELETED
     * @return type
     */
    public function delete() {
        if (!($post = $this->input->post()) || !($type = array_get($post, 'type')) || !($id = array_get($post, 'id')))
            return $this->render_json(['success' => FALSE, 'error' => 'Некорректные параметры запроса']);

        // types with model names
        $types = [
            'post' => 'Posts_Model',
            'object' => 'Object_Model',
            'geo_area' => 'Geo_Area_Model',
            'populated_locality' => 'Populated_Locality_Model',
            'district' => 'District_Model',
            'square' => 'Square_Model',
            'organization' => 'Organizations_Model',
            'glossary' => 'Glossary_Model',
            'metro_line' => 'Metro_Line_Model',
            'metro_station' => 'Metro_Station_Model',
            'populated_locality_type' => 'Populated_Locality_Type_Model',
            'tag' => 'Tags_Model',
        ];

        if (!in_array($type, array_keys($types)))
            return $this->render_json(['success' => FALSE, 'error' => 'Неизвестный тип объекта. Невозможно удалить.']);

        // подключаем модель если еще не подключена
        if (!$this->$types[$type])
            $this->load->model($types[$type]);

        $model = $this->$types[$type];

        // если модель поддерживает безвозвратное - удаляем навсегда
        if (method_exists($model, 'delete_permanently')){
            $model->delete_permanently($id);
            return $this->render_json(['success' => TRUE,]);
        }
//            return $this->render_json(['success' => FALSE, 'error' => 'Метод удаления не реализован у данного типа объектов.']);
        // все объекты удаляем напрямую, возможно понадобится индивидуальное удаление - можно заюзать delete в конкретной модели
        // получаем объект
        if (!($obj = $this->db->where($primary_key = $model->get_primary_key(), $id)->where('status !=', MY_Model::STATUS_DELETED)->get($table = $model->get_table())->row_array()))
            return $this->render_json(['success' => FALSE, 'error' => 'Объект для удаления не найден.']);

        $this->db->where($primary_key, $obj[$primary_key])->update($table, ['status' => MY_Model::STATUS_DELETED]);

        return $this->render_json(['success' => TRUE,]);
    }

    /**
     * Поиск по модели
     * @return boolean
     */
    public function search() {
        $success = FALSE;
        $data = [];
        $get = $this->input->get();
        // определяем модель
        if (!($model_name = array_get($get, 'model')))
            return $this->render_json(array('success' => $success, 'data' => $data, 'error' => 'Необходимо задать модель.', 'input' => $get));
        // существует ли данная модель
        if (!file_exists(APPPATH . 'models' . DIRECTORY_SEPARATOR . $model_name . '.php'))
            return $this->render_json(array('success' => $success, 'data' => $data, 'error' => 'Модель не найдена.', 'input' => $get));
        
        $this->load->model($model_name);
        
        // метод search в модели
        if (!method_exists($this->$model_name, 'search'))
            return $this->render_json(array('success' => $success, 'data' => $data, 'error' => 'Модель не поддерживает поиск.', 'input' => $get));

        $data = $this->$model_name->search($get);

        return $this->render_json(array('success' => !!$data, 'data' => $data, 'input' => $get));
    }

}
