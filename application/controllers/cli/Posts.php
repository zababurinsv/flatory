<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!in_array(PHP_SAPI, array('cgi-fcgi', 'cli')))
    exit('No web access allowed');

/**
 * restore news & articles in  posts
 *
 * @date 04.08.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Posts extends CI_Controller {

    /**
     * Model default
     * @var \Posts_Model 
     */
    public $Posts_Model;

    /**
     * Model
     * @var \File_Categories 
     */
    public $File_Categories;

    /**
     * Model
     * @var \Tags_Model
     */
    public $Tags_Model;

    public function __construct() {
        parent::__construct();

        $this->load->model('Posts_Model');
        $this->load->model('File_Categories');
        $this->load->model('Tags_Model');
    }

    public function up() {

        $this->query("SET foreign_key_checks = 0;");
        $this->query("TRUNCATE TABLE " . $this->db->dbprefix . "posts_tags;");
        $this->query("TRUNCATE TABLE " . $this->db->dbprefix . "posts;");
        $this->query("SET foreign_key_checks = 1;");

        $restore = array('news', 'article');

        foreach ($restore as $type) {

            $category = $this->File_Categories->get_by_field('prefix', $type === 'article' ? $type . 's' : $type);

            if (empty($category))
                throw new Exception('Category ' . $type . ' not found');

            $list = $this->db->get($type)->result_array();

            foreach ($list as $it) {
                
//                vdump($it);
                
                if (!element('file_id', $it, '')) {
                    print 'SKIP ' . $type . ' id: ' . element('id', $it, '??') . PHP_EOL;
                    continue;
                }

                $post = array(
                    'file_category_id' => element('file_category_id', $category, ''),
                    'name' => element('name', $it, ''),
                    'content' => element('content', $it, ''),
                    'anons' => element('anons', $it, ''),
                    'alias' => element('alias', $it, ''),
                    'title' => element('title', $it, ''),
                    'keywords' => element('keywords', $it, ''),
                    'description' => element('description', $it, ''),
                    'created' => element('date', $it, ''),
                    'file_id' => element('file_id', $it, ''),
                    'status' => MY_Model::STATUS_ACTIVE,
                );
                $this->Posts_Model->insert($post);
            }
        }
    }

    private function query($query) {
        $this->db->query($query);
    }

    public function tags() {
        $tags = $this->Tags_Model->get_list(FALSE);

        foreach ($tags as $it) {

            if (!$it['alias'] && $it['name']) {
                $alias = transliteration($it['name']);

                if ($this->Tags_Model->get_by_field('alias', $alias))
                    $alias = $alias . '_' . time();

                $this->Tags_Model->update_by_primary_key($it['tag_id'], array(
                    'name' => $it['name'],
                    'alias' => $alias,
                ));
            }
        }
    }

    private function log($message) {
        print $message . PHP_EOL;
    }

    /**
     * перевод связанных объектов в посты
     */
    public function objects() {

        // получаем связанные обьекты
        $objects_relations = $this->Posts_Model->get_type_object_relations();

        foreach ($objects_relations as $key => $item) {

            $table = array_get($item, 'table', '');

            $this->log('==============================');
            $this->log('Start add by table: "' . $table . '"');

            // получаем категорию обьекта
            // skip if category not found
            if (!($category = $this->File_Categories->get_by_field('prefix', $table, TRUE)) || !($file_category_id = (int) array_get($category, 'file_category_id'))) {
                $this->log('Category by prefix "' . $table . '" not found. Skip!');
                continue;
            }

            // получаем список обьектов данной категории
            // list of hanbk
            $list = $this->Posts_Model->get_all($item);
            $this->log('Find ' . count($list) . ' rows.');

            // создаем пост на основе каждого обьекта
            foreach ($list as $it) {

                $new = array(
                    'name' => $name = array_get($it, array_get($item, 'label', ''), ''),
                    'alias' => $alias = $this->Posts_Model->create_alias($name),
                    'anons' => '',
                    'content' => '',
                    'file_category_id' => $file_category_id,
                    'file_id' => 0,
                    'title' => $name,
                    'keywords' => '',
                    'description' => '',
                    'created' => date("Y-m-d H:i:s"),
                    'object_id' => $object_id = (int) array_get($it, array_get($item, 'primary_key', ''), ''),
                    'status' => 1,
                );

                // check alias with current category_id
                $check = $this->Posts_Model->get_all(['where' => [
                        ['file_category_id' => $file_category_id, 'alias' => $alias],
                ]]);
                // если такой алиас существует в постах данной категории - меняем алиас - список обьектов может содержать дублирующие названия
                // if check exists - add in alias time mark
                if ($check) {
                    $new['alias'] = $alias . '_' . time();
                }
                $id = $this->Posts_Model->insert($new);

                $this->log(!!$id ? 'Inserted ' . $alias . ' object_id: ' . $object_id . ' id: ' . $id : 'Not inserted ' . $alias . ' object_id: ' . $object_id . '!!!');
            }
        }
    }

    public function update_names_geo($param) {

        $list = [
            ['file_category_id' => 15, 'object_id' => 37, 'name' => 'Новостройки в городском округе Балашиха'],
            ['file_category_id' => 15, 'object_id' => 38, 'name' => 'Новостройки в городском округе Бронницы'],
            ['file_category_id' => 15, 'object_id' => 1, 'name' => 'Новостройки в Волоколамском районе'],
            ['file_category_id' => 15, 'object_id' => 2, 'name' => 'Новостройки в Воскресенском районе'],
            ['file_category_id' => 15, 'object_id' => 39, 'name' => 'Новостройки в городском округе Дзержинский'],
            ['file_category_id' => 15, 'object_id' => 3, 'name' => 'Новостройки в Дмитровском районе МО'],
            ['file_category_id' => 15, 'object_id' => 40, 'name' => 'Новостройки в городском округе Долгопрудный'],
            ['file_category_id' => 15, 'object_id' => 41, 'name' => 'Новостройки в городском округе Домодедово'],
            ['file_category_id' => 15, 'object_id' => 42, 'name' => 'Новостройки в городском округе Дубна'],
            ['file_category_id' => 15, 'object_id' => 4, 'name' => 'Новостройки в Егорьевском районе'],
            ['file_category_id' => 15, 'object_id' => 43, 'name' => 'Новостройки в городском округе Железнодорожный'],
            ['file_category_id' => 15, 'object_id' => 44, 'name' => 'Новостройки в городском округе Жуковский'],
            ['file_category_id' => 15, 'object_id' => 5, 'name' => 'Новостройки в Зарайском районе'],
            ['file_category_id' => 15, 'object_id' => 45, 'name' => 'Новостройки в городском округе Звенигород'],
            ['file_category_id' => 15, 'object_id' => 46, 'name' => 'Новостройки в городском округе Ивантеевка'],
            ['file_category_id' => 15, 'object_id' => 6, 'name' => 'Новостройки в Истринском районе'],
            ['file_category_id' => 15, 'object_id' => 7, 'name' => 'Новостройки в Каширском районе'],
            ['file_category_id' => 15, 'object_id' => 47, 'name' => 'Новостройки в городском округе Климовск'],
            ['file_category_id' => 15, 'object_id' => 8, 'name' => 'Новостройки в Клинском районе'],
            ['file_category_id' => 15, 'object_id' => 9, 'name' => 'Новостройки в Коломенском районе'],
            ['file_category_id' => 15, 'object_id' => 48, 'name' => 'Новостройки в городском округе Коломна'],
            ['file_category_id' => 15, 'object_id' => 49, 'name' => 'Новостройки в городском округе Королёв'],
            ['file_category_id' => 15, 'object_id' => 50, 'name' => 'Новостройки в городском округе Котельники'],
            ['file_category_id' => 15, 'object_id' => 51, 'name' => 'Новостройки в городском округе Красноармейск'],
            ['file_category_id' => 15, 'object_id' => 10, 'name' => 'Новостройки в Красногорском районе'],
            ['file_category_id' => 15, 'object_id' => 11, 'name' => 'Новостройки в Ленинском районе'],
            ['file_category_id' => 15, 'object_id' => 52, 'name' => 'Новостройки в городском округе Лобня'],
            ['file_category_id' => 15, 'object_id' => 53, 'name' => 'Новостройки в городском округе Лосино-Петровский'],
            ['file_category_id' => 15, 'object_id' => 12, 'name' => 'Новостройки в Лотошинском районе'],
            ['file_category_id' => 15, 'object_id' => 13, 'name' => 'Новостройки в Луховицком районе'],
            ['file_category_id' => 15, 'object_id' => 54, 'name' => 'Новостройки в городском округе Лыткарено'],
            ['file_category_id' => 15, 'object_id' => 14, 'name' => 'Новостройки в Люберецком районе'],
            ['file_category_id' => 15, 'object_id' => 15, 'name' => 'Новостройки в Можайском районе МО'],
            ['file_category_id' => 15, 'object_id' => 16, 'name' => 'Новостройки в Мытищинском районе'],
            ['file_category_id' => 15, 'object_id' => 17, 'name' => 'Новостройки в Наро-Фоминском районе'],
            ['file_category_id' => 15, 'object_id' => 18, 'name' => 'Новостройки в Ногинском районе'],
            ['file_category_id' => 15, 'object_id' => 19, 'name' => 'Новостройки в Одинцовском районе'],
            ['file_category_id' => 15, 'object_id' => 20, 'name' => 'Новостройки в Озёрском районе'],
            ['file_category_id' => 15, 'object_id' => 55, 'name' => 'Новостройки в городском округе Орехово-Зуево'],
            ['file_category_id' => 15, 'object_id' => 21, 'name' => 'Новостройки в Орехово-Зуевском районе'],
            ['file_category_id' => 15, 'object_id' => 22, 'name' => 'Новостройки в Павлово-Посадском районе'],
            ['file_category_id' => 15, 'object_id' => 56, 'name' => 'Новостройки в городском округе Подольск'],
            ['file_category_id' => 15, 'object_id' => 23, 'name' => 'Новостройки в Подольском районе'],
            ['file_category_id' => 15, 'object_id' => 57, 'name' => 'Новостройки в городском округе Протвино'],
            ['file_category_id' => 15, 'object_id' => 24, 'name' => 'Новостройки в Пушкинском районе'],
            ['file_category_id' => 15, 'object_id' => 58, 'name' => 'Новостройки в городском округе Пущино'],
            ['file_category_id' => 15, 'object_id' => 25, 'name' => 'Новостройки в Раменском районе'],
            ['file_category_id' => 15, 'object_id' => 59, 'name' => 'Новостройки в городском округе Реутов'],
            ['file_category_id' => 15, 'object_id' => 60, 'name' => 'Новостройки в городском округе Рошаль'],
            ['file_category_id' => 15, 'object_id' => 26, 'name' => 'Новостройки в Рузском районе'],
            ['file_category_id' => 15, 'object_id' => 28, 'name' => 'Новостройки в Сергиево-Посадском районе'],
            ['file_category_id' => 15, 'object_id' => 27, 'name' => 'Новостройки в Серебряно-Прудском районе'],
            ['file_category_id' => 15, 'object_id' => 61, 'name' => 'Новостройки в городском округе Серпухов'],
            ['file_category_id' => 15, 'object_id' => 29, 'name' => 'Новостройки в Серпуховском районе'],
            ['file_category_id' => 15, 'object_id' => 30, 'name' => 'Новостройки в Солнечногорском районе'],
            ['file_category_id' => 15, 'object_id' => 31, 'name' => 'Новостройки в Ступинском районе'],
            ['file_category_id' => 15, 'object_id' => 32, 'name' => 'Новостройки в Талдомском районе'],
            ['file_category_id' => 15, 'object_id' => 62, 'name' => 'Новостройки в городском округе Фрязино'],
            ['file_category_id' => 15, 'object_id' => 63, 'name' => 'Новостройки в городском округе Химки'],
            ['file_category_id' => 15, 'object_id' => 64, 'name' => 'Новостройки в городском округе Черноголовка'],
            ['file_category_id' => 15, 'object_id' => 33, 'name' => 'Новостройки в Чеховском районе'],
            ['file_category_id' => 15, 'object_id' => 34, 'name' => 'Новостройки в Шатурском районе'],
            ['file_category_id' => 15, 'object_id' => 35, 'name' => 'Новостройки в Шаховском районе'],
            ['file_category_id' => 15, 'object_id' => 36, 'name' => 'Новостройки в Щёлковском районе'],
            ['file_category_id' => 15, 'object_id' => 65, 'name' => 'Новостройки в городском округе Электрогорск'],
            ['file_category_id' => 15, 'object_id' => 66, 'name' => 'Новостройки в городском округе Электросталь'],
            ['file_category_id' => 15, 'object_id' => 67, 'name' => 'Новостройки в городском округе Юбилейный'],
            ['file_category_id' => 16, 'object_id' => 15, 'name' => 'Новостройки в Андреевке'],
            ['file_category_id' => 16, 'object_id' => 20, 'name' => 'Новостройки в Апрелевке'],
            ['file_category_id' => 16, 'object_id' => 4, 'name' => 'Новостройки в Балашихе'],
            ['file_category_id' => 16, 'object_id' => 18, 'name' => 'Новостройки в Боброво'],
            ['file_category_id' => 16, 'object_id' => 53, 'name' => 'Новостройки в Болтино'],
            ['file_category_id' => 16, 'object_id' => 31, 'name' => 'Новостройки в Болтино'],
            ['file_category_id' => 16, 'object_id' => 34, 'name' => 'Новостройки в Больших Вязёмах'],
            ['file_category_id' => 16, 'object_id' => 13, 'name' => 'Новостройки в Брёхово'],
            ['file_category_id' => 16, 'object_id' => 16, 'name' => 'Новостройки в Видном'],
            ['file_category_id' => 16, 'object_id' => 46, 'name' => 'Новостройки в Гальчино'],
            ['file_category_id' => 16, 'object_id' => 24, 'name' => 'Новостройки в деревне Голубое'],
            ['file_category_id' => 16, 'object_id' => 57, 'name' => 'Новостройки в деревне Голубое'],
            ['file_category_id' => 16, 'object_id' => 45, 'name' => 'Новостройки в посёлке Горки-10'],
            ['file_category_id' => 16, 'object_id' => 9, 'name' => 'Новостройки в Долгопрудном'],
            ['file_category_id' => 16, 'object_id' => 17, 'name' => 'Новостройки в Домодедово'],
            ['file_category_id' => 16, 'object_id' => 47, 'name' => 'Новостройки в Дрожжино'],
            ['file_category_id' => 16, 'object_id' => 14, 'name' => 'Новостройки в Елино'],
            ['file_category_id' => 16, 'object_id' => 3, 'name' => 'Новостройки в Железнодорожном'],
            ['file_category_id' => 16, 'object_id' => 41, 'name' => 'Новостройки в Жуково'],
            ['file_category_id' => 16, 'object_id' => 19, 'name' => 'Новостройки в Жуковском'],
            ['file_category_id' => 16, 'object_id' => 63, 'name' => 'Новостройки в Заречье'],
            ['file_category_id' => 16, 'object_id' => 32, 'name' => 'Новостройки в Звенигороде'],
            ['file_category_id' => 16, 'object_id' => 62, 'name' => 'Новостройки в Зелёном городе'],
            ['file_category_id' => 16, 'object_id' => 48, 'name' => 'Новостройки в посёлке Ильинское-Усово'],
            ['file_category_id' => 16, 'object_id' => 10, 'name' => 'Новостройки в Королёве'],
            ['file_category_id' => 16, 'object_id' => 25, 'name' => 'Новостройки в Котельниках'],
            ['file_category_id' => 16, 'object_id' => 36, 'name' => 'Новостройки в Красноармейске'],
            ['file_category_id' => 16, 'object_id' => 22, 'name' => 'Новостройки в Красногорске'],
            ['file_category_id' => 16, 'object_id' => 58, 'name' => 'Новостройки в посёлке Красный'],
            ['file_category_id' => 16, 'object_id' => 27, 'name' => 'Новостройки в Лайково'],
            ['file_category_id' => 16, 'object_id' => 38, 'name' => 'Новостройки в Лобаново'],
            ['file_category_id' => 16, 'object_id' => 33, 'name' => 'Новостройки в Лобне'],
            ['file_category_id' => 16, 'object_id' => 43, 'name' => 'Новостройки в Лопатино'],
            ['file_category_id' => 16, 'object_id' => 52, 'name' => 'Новостройки в Лосино-Петровском'],
            ['file_category_id' => 16, 'object_id' => 26, 'name' => 'Новостройки в Лыткарено'],
            ['file_category_id' => 16, 'object_id' => 23, 'name' => 'Новостройки в Люберцах'],
            ['file_category_id' => 16, 'object_id' => 55, 'name' => 'Новостройки в Митькино'],
            ['file_category_id' => 16, 'object_id' => 64, 'name' => 'Новостройки в Молоково'],
            ['file_category_id' => 16, 'object_id' => 1, 'name' => 'Новостройки в Москве'],
            ['file_category_id' => 16, 'object_id' => 28, 'name' => 'Новостройки в Мотяково'],
            ['file_category_id' => 16, 'object_id' => 11, 'name' => 'Новостройки в Мытищах'],
            ['file_category_id' => 16, 'object_id' => 60, 'name' => 'Новостройки в Наро-Фоминске'],
            ['file_category_id' => 16, 'object_id' => 50, 'name' => 'Новостройки в Нахабино'],
            ['file_category_id' => 16, 'object_id' => 54, 'name' => 'Новостройки в поселении Новоивановское'],
            ['file_category_id' => 16, 'object_id' => 44, 'name' => 'Новостройки в Ногинске'],
            ['file_category_id' => 16, 'object_id' => 21, 'name' => 'Новостройки в Одинцово'],
            ['file_category_id' => 16, 'object_id' => 51, 'name' => 'Новостройки в Островцах'],
            ['file_category_id' => 16, 'object_id' => 8, 'name' => 'Новостройки в посёлке Отрадном'],
            ['file_category_id' => 16, 'object_id' => 56, 'name' => 'Новостройки в Павловской Слободе'],
            ['file_category_id' => 16, 'object_id' => 42, 'name' => 'Новостройки в Пирогово'],
            ['file_category_id' => 16, 'object_id' => 40, 'name' => 'Новостройки в Поливаново'],
            ['file_category_id' => 16, 'object_id' => 7, 'name' => 'Новостройки в Путилково'],
            ['file_category_id' => 16, 'object_id' => 29, 'name' => 'Новостройки в городе Раменское'],
            ['file_category_id' => 16, 'object_id' => 5, 'name' => 'Новостройки в Реутове'],
            ['file_category_id' => 16, 'object_id' => 30, 'name' => 'Новостройки в Ромашково'],
            ['file_category_id' => 16, 'object_id' => 37, 'name' => 'Новостройки в Рузино'],
            ['file_category_id' => 16, 'object_id' => 6, 'name' => 'Новостройки в Сабурово'],
            ['file_category_id' => 16, 'object_id' => 49, 'name' => 'Новостройки в Сухарево'],
            ['file_category_id' => 16, 'object_id' => 61, 'name' => 'Новостройки в Томилино'],
            ['file_category_id' => 16, 'object_id' => 12, 'name' => 'Новостройки в Химках'],
            ['file_category_id' => 16, 'object_id' => 35, 'name' => 'Новостройки в Щёлково'],
            ['file_category_id' => 16, 'object_id' => 39, 'name' => 'Новостройки в Электроуглях'],
            ['file_category_id' => 17, 'object_id' => 4, 'name' => 'Новостройки в Восточном АО'],
            ['file_category_id' => 17, 'object_id' => 7, 'name' => 'Новостройки в Западном АО'],
            ['file_category_id' => 17, 'object_id' => 9, 'name' => 'Новостройки в Зеленограде'],
            ['file_category_id' => 17, 'object_id' => 2, 'name' => 'Новостройки в Северном АО'],
            ['file_category_id' => 17, 'object_id' => 3, 'name' => 'Новостройки в Северо-Восточном АО'],
            ['file_category_id' => 17, 'object_id' => 8, 'name' => 'Новостройки в Северо-Западном АО'],
            ['file_category_id' => 17, 'object_id' => 10, 'name' => 'Новостройки в Троицком АО'],
            ['file_category_id' => 17, 'object_id' => 1, 'name' => 'Новостройки в Центральном АО'],
            ['file_category_id' => 17, 'object_id' => 6, 'name' => 'Новостройки в Юго-Восточном АО'],
            ['file_category_id' => 17, 'object_id' => 11, 'name' => 'Новостройки в Юго-Западном АО'],
            ['file_category_id' => 17, 'object_id' => 5, 'name' => 'Новостройки в Южном АО Москвы'],
            ['file_category_id' => 18, 'object_id' => 88, 'name' => 'Новостройки в Академическом районе'],
            ['file_category_id' => 18, 'object_id' => 27, 'name' => 'Новостройки в Алексеевском районе'],
            ['file_category_id' => 18, 'object_id' => 28, 'name' => 'Новостройки в Алтуфьевском районе'],
            ['file_category_id' => 18, 'object_id' => 1, 'name' => 'Новостройки на Арбате'],
            ['file_category_id' => 18, 'object_id' => 11, 'name' => 'Новостройки в районе Аэропорт'],
            ['file_category_id' => 18, 'object_id' => 29, 'name' => 'Новостройки в Бабушкинском районе'],
            ['file_category_id' => 18, 'object_id' => 2, 'name' => 'Новостройки в Басманном районе'],
            ['file_category_id' => 18, 'object_id' => 12, 'name' => 'Новостройки в районе Беговой'],
            ['file_category_id' => 18, 'object_id' => 13, 'name' => 'Новостройки в Бескудниковском районе'],
            ['file_category_id' => 18, 'object_id' => 30, 'name' => 'Новостройки в Бибирево'],
            ['file_category_id' => 18, 'object_id' => 73, 'name' => 'Новостройки в Бирюлёво Восточное'],
            ['file_category_id' => 18, 'object_id' => 72, 'name' => 'Новостройки в Бирюлёво Западное'],
            ['file_category_id' => 18, 'object_id' => 44, 'name' => 'Новостройки в Богородском районе'],
            ['file_category_id' => 18, 'object_id' => 74, 'name' => 'Новостройки в Братеево'],
            ['file_category_id' => 18, 'object_id' => 31, 'name' => 'Новостройки в Бутырском районе'],
            ['file_category_id' => 18, 'object_id' => 45, 'name' => 'Новостройки в Вешняках'],
            ['file_category_id' => 18, 'object_id' => 100, 'name' => 'Новостройки во Внуково'],
            ['file_category_id' => 18, 'object_id' => 128, 'name' => 'Новостройки в поселении Внуковское'],
            ['file_category_id' => 18, 'object_id' => 14, 'name' => 'Новостройки в Войковском районе'],
            ['file_category_id' => 18, 'object_id' => 126, 'name' => 'Новостройки в поселении Вороновское'],
            ['file_category_id' => 18, 'object_id' => 127, 'name' => 'Новостройки в поселении Воскресенское'],
            ['file_category_id' => 18, 'object_id' => 15, 'name' => 'Новостройки в Восточном Дегунино'],
            ['file_category_id' => 18, 'object_id' => 46, 'name' => 'Новостройки в Восточном Измайлово'],
            ['file_category_id' => 18, 'object_id' => 60, 'name' => 'Новостройки в Выхино-Жулебино'],
            ['file_category_id' => 18, 'object_id' => 89, 'name' => 'Новостройки в Гагаринском районе'],
            ['file_category_id' => 18, 'object_id' => 16, 'name' => 'Новостройки в Головинском районе'],
            ['file_category_id' => 18, 'object_id' => 47, 'name' => 'Новостройки в Гольяново'],
            ['file_category_id' => 18, 'object_id' => 75, 'name' => 'Новостройки в Даниловском районе'],
            ['file_category_id' => 18, 'object_id' => 129, 'name' => 'Новостройки в поселении Десёновское'],
            ['file_category_id' => 18, 'object_id' => 17, 'name' => 'Новостройки в Дмитровском районе'],
            ['file_category_id' => 18, 'object_id' => 76, 'name' => 'Новостройки в Донском районе'],
            ['file_category_id' => 18, 'object_id' => 101, 'name' => 'Новостройки в Дорогомилово'],
            ['file_category_id' => 18, 'object_id' => 3, 'name' => 'Новостройки в Замоскворечье'],
            ['file_category_id' => 18, 'object_id' => 18, 'name' => 'Новостройки в Западном Дегунино'],
            ['file_category_id' => 18, 'object_id' => 90, 'name' => 'Новостройки в Зюзино'],
            ['file_category_id' => 18, 'object_id' => 77, 'name' => 'Новостройки в Зябликово'],
            ['file_category_id' => 18, 'object_id' => 48, 'name' => 'Новостройки в Ивановском'],
            ['file_category_id' => 18, 'object_id' => 49, 'name' => 'Новостройки в Измайлово'],
            ['file_category_id' => 18, 'object_id' => 61, 'name' => 'Новостройки в Капотне'],
            ['file_category_id' => 18, 'object_id' => 130, 'name' => 'Новостройки в поселении Киевский'],
            ['file_category_id' => 18, 'object_id' => 131, 'name' => 'Новостройки в поселении Клёновское'],
            ['file_category_id' => 18, 'object_id' => 132, 'name' => 'Новостройки в поселении Кокошкино'],
            ['file_category_id' => 18, 'object_id' => 91, 'name' => 'Новостройки в Коньково'],
            ['file_category_id' => 18, 'object_id' => 19, 'name' => 'Новостройки в Коптево'],
            ['file_category_id' => 18, 'object_id' => 50, 'name' => 'Новостройки в Косино-Ухтомском районе'],
            ['file_category_id' => 18, 'object_id' => 92, 'name' => 'Новостройки в Котловке'],
            ['file_category_id' => 18, 'object_id' => 133, 'name' => 'Новостройки в поселении Краснопахорское'],
            ['file_category_id' => 18, 'object_id' => 4, 'name' => 'Новостройки в Красносельском районе'],
            ['file_category_id' => 18, 'object_id' => 102, 'name' => 'Новостройки в Крылатском'],
            ['file_category_id' => 18, 'object_id' => 121, 'name' => 'Новостройки в Крюково'],
            ['file_category_id' => 18, 'object_id' => 62, 'name' => 'Новостройки в Кузьминках'],
            ['file_category_id' => 18, 'object_id' => 103, 'name' => 'Новостройки в Кунцево'],
            ['file_category_id' => 18, 'object_id' => 113, 'name' => 'Новостройки в Куркино'],
            ['file_category_id' => 18, 'object_id' => 20, 'name' => 'Новостройки в Левобережном районе'],
            ['file_category_id' => 18, 'object_id' => 63, 'name' => 'Новостройки в Лефортово'],
            ['file_category_id' => 18, 'object_id' => 32, 'name' => 'Новостройки в Лианозово'],
            ['file_category_id' => 18, 'object_id' => 93, 'name' => 'Новостройки в Ломоносовском районе'],
            ['file_category_id' => 18, 'object_id' => 33, 'name' => 'Новостройки в Лосиноостровском районе'],
            ['file_category_id' => 18, 'object_id' => 64, 'name' => 'Новостройки в Люблино'],
            ['file_category_id' => 18, 'object_id' => 134, 'name' => 'Новостройки в поселении Марушкинское'],
            ['file_category_id' => 18, 'object_id' => 34, 'name' => 'Новостройки в Марфино'],
            ['file_category_id' => 18, 'object_id' => 35, 'name' => 'Новостройки в Марьиной Роще'],
            ['file_category_id' => 18, 'object_id' => 65, 'name' => 'Новостройки в Марьино'],
            ['file_category_id' => 18, 'object_id' => 122, 'name' => 'Новостройки в Матушкино'],
            ['file_category_id' => 18, 'object_id' => 51, 'name' => 'Новостройки в Метрогородке'],
            ['file_category_id' => 18, 'object_id' => 5, 'name' => 'Новостройки в Мещанском районе'],
            ['file_category_id' => 18, 'object_id' => 114, 'name' => 'Новостройки в Митино'],
            ['file_category_id' => 18, 'object_id' => 135, 'name' => 'Новостройки в поселении Михайлово-Ярцевское'],
            ['file_category_id' => 18, 'object_id' => 104, 'name' => 'Новостройки в Можайском районе'],
            ['file_category_id' => 18, 'object_id' => 21, 'name' => 'Новостройки в Молжаниновском районе'],
            ['file_category_id' => 18, 'object_id' => 78, 'name' => 'Новостройки в Москворечье-Сабурово'],
            ['file_category_id' => 18, 'object_id' => 136, 'name' => 'Новостройки в поселении Московский'],
            ['file_category_id' => 18, 'object_id' => 137, 'name' => 'Новостройки в поселении Мосрентген'],
            ['file_category_id' => 18, 'object_id' => 79, 'name' => 'Новостройки в Нагатино-Садовники'],
            ['file_category_id' => 18, 'object_id' => 80, 'name' => 'Новостройки в Нагатинском районе'],
            ['file_category_id' => 18, 'object_id' => 81, 'name' => 'Новостройки в Нагорном районе'],
            ['file_category_id' => 18, 'object_id' => 66, 'name' => 'Новостройки в Нижегородском районе'],
            ['file_category_id' => 18, 'object_id' => 105, 'name' => 'Новостройки в Ново-Переделкино'],
            ['file_category_id' => 18, 'object_id' => 52, 'name' => 'Новостройки в Новогиреево'],
            ['file_category_id' => 18, 'object_id' => 53, 'name' => 'Новостройки в Новокосино'],
            ['file_category_id' => 18, 'object_id' => 138, 'name' => 'Новостройки в поселении Новофёдоровское'],
            ['file_category_id' => 18, 'object_id' => 94, 'name' => 'Новостройки в Обручевском районе'],
            ['file_category_id' => 18, 'object_id' => 82, 'name' => 'Новостройки в районе Орехово-Борисово Северное'],
            ['file_category_id' => 18, 'object_id' => 83, 'name' => 'Новостройки в районе Орехово-Борисово Южное'],
            ['file_category_id' => 18, 'object_id' => 36, 'name' => 'Новостройки в Останкинском районе'],
            ['file_category_id' => 18, 'object_id' => 37, 'name' => 'Новостройки в Отрадном'],
            ['file_category_id' => 18, 'object_id' => 106, 'name' => 'Новостройки в районе Очаково-Матвеевское'],
            ['file_category_id' => 18, 'object_id' => 139, 'name' => 'Новостройки в поселении Первомайское'],
            ['file_category_id' => 18, 'object_id' => 54, 'name' => 'Новостройки в Перово'],
            ['file_category_id' => 18, 'object_id' => 67, 'name' => 'Новостройки в Печатниках'],
            ['file_category_id' => 18, 'object_id' => 115, 'name' => 'Новостройки в районе Покровское-Стрешнево'],
            ['file_category_id' => 18, 'object_id' => 55, 'name' => 'Новостройки в поселке Восточный'],
            ['file_category_id' => 18, 'object_id' => 68, 'name' => 'Новостройки в поселке Некрасовка'],
            ['file_category_id' => 18, 'object_id' => 56, 'name' => 'Новостройки в Преображенском районе'],
            ['file_category_id' => 18, 'object_id' => 6, 'name' => 'Новостройки в Пресненском районе'],
            ['file_category_id' => 18, 'object_id' => 107, 'name' => 'Новостройки на проспекте Вернадского'],
            ['file_category_id' => 18, 'object_id' => 108, 'name' => 'Новостройки в Раменках'],
            ['file_category_id' => 18, 'object_id' => 140, 'name' => 'Новостройки в поселении Роговское'],
            ['file_category_id' => 18, 'object_id' => 39, 'name' => 'Новостройки в Ростокино'],
            ['file_category_id' => 18, 'object_id' => 141, 'name' => 'Новостройки в поселении  Рязановское'],
            ['file_category_id' => 18, 'object_id' => 69, 'name' => 'Новостройки в Рязанском районе'],
            ['file_category_id' => 18, 'object_id' => 123, 'name' => 'Новостройки в районе Савёлки'],
            ['file_category_id' => 18, 'object_id' => 22, 'name' => 'Новостройки в Савёловском районе'],
            ['file_category_id' => 18, 'object_id' => 40, 'name' => 'Новостройки в Свиблово'],
            ['file_category_id' => 18, 'object_id' => 95, 'name' => 'Новостройки в Северном Бутово'],
            ['file_category_id' => 18, 'object_id' => 57, 'name' => 'Новостройки в Северном Измайлово'],
            ['file_category_id' => 18, 'object_id' => 41, 'name' => 'Новостройки в Северном Медведково'],
            ['file_category_id' => 18, 'object_id' => 116, 'name' => 'Новостройки в Северном Тушино'],
            ['file_category_id' => 18, 'object_id' => 38, 'name' => 'Новостройки в районе Северный'],
            ['file_category_id' => 18, 'object_id' => 124, 'name' => 'Новостройки в Силино'],
            ['file_category_id' => 18, 'object_id' => 23, 'name' => 'Новостройки в районе Сокол'],
            ['file_category_id' => 18, 'object_id' => 58, 'name' => 'Новостройки в районе Соколиная Гора'],
            ['file_category_id' => 18, 'object_id' => 59, 'name' => 'Новостройки в Сокольниках'],
            ['file_category_id' => 18, 'object_id' => 109, 'name' => 'Новостройки в Солнцево'],
            ['file_category_id' => 18, 'object_id' => 142, 'name' => 'Новостройки в поселении Сосенское'],
            ['file_category_id' => 18, 'object_id' => 125, 'name' => 'Новостройки в районе Старое Крюково'],
            ['file_category_id' => 18, 'object_id' => 117, 'name' => 'Новостройки в Строгино'],
            ['file_category_id' => 18, 'object_id' => 7, 'name' => 'Новостройки в Таганском районе'],
            ['file_category_id' => 18, 'object_id' => 8, 'name' => 'Новостройки в Тверском районе'],
            ['file_category_id' => 18, 'object_id' => 70, 'name' => 'Новостройки в Текстильщиках'],
            ['file_category_id' => 18, 'object_id' => 96, 'name' => 'Новостройки в Тёплом Стане'],
            ['file_category_id' => 18, 'object_id' => 24, 'name' => 'Новостройки в Тимирязевском районе'],
            ['file_category_id' => 18, 'object_id' => 143, 'name' => 'Новостройки в Троицке'],
            ['file_category_id' => 18, 'object_id' => 110, 'name' => 'Новостройки в Тропарёво-Никулино'],
            ['file_category_id' => 18, 'object_id' => 111, 'name' => 'Новостройки в районе Филёвский Парк'],
            ['file_category_id' => 18, 'object_id' => 112, 'name' => 'Новостройки в районе Фили-Давыдково'],
            ['file_category_id' => 18, 'object_id' => 144, 'name' => 'Новостройки в поселении Филимонковское'],
            ['file_category_id' => 18, 'object_id' => 9, 'name' => 'Новостройки в Хамовниках'],
            ['file_category_id' => 18, 'object_id' => 25, 'name' => 'Новостройки в Ховрино'],
            ['file_category_id' => 18, 'object_id' => 118, 'name' => 'Новостройки в Хорошёво-Мнёвники'],
            ['file_category_id' => 18, 'object_id' => 26, 'name' => 'Новостройки в Хорошёвском районе'],
            ['file_category_id' => 18, 'object_id' => 84, 'name' => 'Новостройки в Царицыно'],
            ['file_category_id' => 18, 'object_id' => 97, 'name' => 'Новостройки в Черёмушках'],
            ['file_category_id' => 18, 'object_id' => 85, 'name' => 'Новостройки в Чертаново Северное'],
            ['file_category_id' => 18, 'object_id' => 86, 'name' => 'Новостройки в Чертаново Центральное'],
            ['file_category_id' => 18, 'object_id' => 87, 'name' => 'Новостройки в Чертаново Южное'],
            ['file_category_id' => 18, 'object_id' => 145, 'name' => 'Новостройки в поселении Щаповское'],
            ['file_category_id' => 18, 'object_id' => 146, 'name' => 'Новостройки в Щербинке'],
            ['file_category_id' => 18, 'object_id' => 119, 'name' => 'Новостройки в Щукино'],
            ['file_category_id' => 18, 'object_id' => 98, 'name' => 'Новостройки в районе Южное Бутово'],
            ['file_category_id' => 18, 'object_id' => 42, 'name' => 'Новостройки в районе Южное Медведково'],
            ['file_category_id' => 18, 'object_id' => 120, 'name' => 'Новостройки в районе Южное Тушино'],
            ['file_category_id' => 18, 'object_id' => 71, 'name' => 'Новостройки в Южнопортовом районе'],
            ['file_category_id' => 18, 'object_id' => 10, 'name' => 'Новостройки на Якиманке'],
            ['file_category_id' => 18, 'object_id' => 43, 'name' => 'Новостройки в Ярославском районе'],
            ['file_category_id' => 18, 'object_id' => 99, 'name' => 'Новостройки в Ясенево'],
        ];

        foreach ($list as $it) {

            $this->db->query("UPDATE " . $this->db->dbprefix . "posts SET name = '" . array_get($it, 'name', '') . "' 
                    WHERE file_category_id = " . (int) array_get($it, 'file_category_id') . " AND object_id = " . (int) array_get($it, 'object_id') . ";");

            print 'file_category_id: ' . (int) array_get($it, 'file_category_id') . ' object_id: ' . (int) array_get($it, 'object_id') . PHP_EOL;
        }
    }

    public function coords_parse_update() {

//        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."meta` CHANGE COLUMN `x` `x` VARCHAR(105) NOT NULL DEFAULT '' , CHANGE COLUMN `y` `y` VARCHAR(105) NOT NULL DEFAULT '' ;");
        $list = $this->db->query("SELECT id, point FROM " . $this->db->dbprefix . "meta;")->result_array();

        foreach ($list as $item) {
            if (!($point = $item['point']))
                continue;

            if (($pos = strpos($point, '&p=')) === false)
                continue;

            $point = substr($point, $pos + 3);

            $point = explode(',', $point);

            if (count($point) !== 2)
                continue;

            $result = ['x' => $point[0], 'y' => $point[1], 'id' => (int) $item['id']];

            $this->db->query("update " . $this->db->dbprefix . "meta set x = '" . $result['x'] . "', y = '" . $result['y'] . "' where id = " . $result['id'] . ";");

            print json_encode($result) . PHP_EOL;
        }
    }

    public function upd_metro() {
        $list = [
            1 => 'Библиотека имени Ленина',
            2 => 'Воробьевы горы',
            3 => 'Комсомольская (СЛ)',
            4 => 'Красносельская',
            5 => 'Красные ворота',
            6 => 'Кропоткинская',
            7 => 'Лубянка',
            8 => 'Охотный ряд',
            9 => 'Парк культуры (СЛ)',
            10 => 'Саларьево',
            11 => 'Проспект Вернадского',
            12 => 'Сокольники',
            13 => 'Спортивная',
            14 => 'Бульвар Рокоссовского',
            15 => 'Университет',
            16 => 'Фрунзенская',
            17 => 'Черкизовская',
            18 => 'Чистые пруды',
            19 => 'Юго-Западная',
            20 => 'Саларьево',
            21 => 'Преображенская площадь',
            22 => 'Борисово',
            23 => 'Братиславская',
            24 => 'Волжская',
            25 => 'Достоевская',
            26 => 'Дубровка',
            27 => 'Зябликово',
            28 => 'Кожуховская',
            29 => 'Кожуховская',
            30 => 'Печатники',
            31 => 'Люблино',
            32 => 'Марьина роща',
            33 => 'Марьино',
            34 => 'Печатники',
            35 => 'Римская',
            36 => 'Сретенский бульвар',
            37 => 'Трубная',
            38 => 'Чкаловская',
            39 => 'Шипиловская',
            40 => 'Бутырская (2016г.)',
            41 => 'Автозаводская',
            42 => 'Алма-Атинская',
            43 => 'Белорусская (ЗЛ)',
            44 => 'Водный стадион',
            45 => 'Войковская',
            46 => 'Динамо',
            47 => 'Домодедовская',
            48 => 'Кантемировская',
            49 => 'Каширская (ЗЛ)',
            50 => 'Коломенская',
            51 => 'Красногвардейская',
            52 => 'Маяковская',
            53 => 'Новокузнецкая',
            54 => 'Орехово',
            55 => 'Павелецкая (ЗЛ)',
            56 => 'Речной вокзал',
            57 => 'Сокол',
            58 => 'Тверская',
            59 => 'Театральная',
            60 => 'Царицыно',
            61 => 'Арбатская (ФЛ)',
            62 => 'Арбатская (АПЛ)',
            63 => 'Бауманская',
            64 => 'Волоколамская',
            65 => 'Измайловская',
            66 => 'Крылатское',
            67 => 'Курская (АПЛ)',
            68 => 'Митино',
            69 => 'Молодежная',
            70 => 'Мякинино',
            71 => 'Парк Победы (АПЛ)',
            72 => 'Партизанская',
            73 => 'Первомайская',
            74 => 'Площадь Революции',
            75 => 'Пятницкое шоссе',
            76 => 'Семеновская',
            77 => 'Славянский бульвар',
            78 => 'Смоленская (АПЛ)',
            79 => 'Строгино',
            80 => 'Щелковская',
            81 => 'Арбатская (Арбатско-Покровская линия) 2',
            82 => 'Арбатская (Арбатско-Покровская линия) 2 3',
            83 => 'Александровский сад',
            84 => 'Багратионовская',
            85 => 'Выставочная',
            86 => 'Киевская (ФЛ)',
            87 => 'Кунцевская (ФЛ)',
            88 => 'Международная',
            89 => 'Пионерская',
            90 => 'Смоленская (ФЛ)',
            91 => 'Студенческая',
            92 => 'Филевский парк',
            93 => 'Фили',
            94 => 'Александровский сад 2',
            95 => 'Добрынинская',
            96 => 'Краснопресненская',
            97 => 'Новослободская',
            98 => 'Октябрьская (КЛ)',
            99 => 'Проспект Мира (КЛ)',
            100 => 'Таганская (КЛ)',
            101 => 'Академическая',
            102 => 'Алексеевская',
            103 => 'Бабушкинская',
            104 => 'Беляево',
            105 => 'Ботанический сад',
            106 => 'ВДНХ',
            107 => 'Калужская',
            108 => 'Китай-город (КРЛ)',
            109 => 'Коньково',
            110 => 'Ленинский проспект',
            111 => 'Медведково',
            112 => 'Новоясеневская',
            113 => 'Новые Черемушки',
            114 => 'Профсоюзная',
            115 => 'Рижская',
            116 => 'Свиблово',
            117 => 'Сухаревская',
            118 => 'Теплый Стан',
            119 => 'Третьяковская (КРЛ)',
            120 => 'Тургеневская',
            121 => 'Шаболовская',
            122 => 'Ясенево',
            123 => 'Баррикадная',
            124 => 'Беговая',
            125 => 'Волгоградский проспект',
            126 => 'Выхино',
            127 => 'Жулебино',
            128 => 'Кузнецкий мост',
            129 => 'Кузьминки',
            130 => 'Октябрьское поле',
            131 => 'Планерная',
            132 => 'Полежаевская',
            133 => 'Пролетарская',
            134 => 'Пушкинская',
            135 => 'Рязанский проспект',
            136 => 'Сходненская',
            137 => 'Текстильщики',
            138 => 'Тушинская',
            139 => 'Улица 1905 года',
            140 => 'Щукинская',
            141 => 'Волгоградский проспект 3',
            142 => 'Авиамоторная',
            143 => 'Деловой центр',
            144 => 'Марксистская',
            145 => 'Новогиреево',
            146 => 'Новокосино',
            147 => 'Перово',
            148 => 'Площадь Ильича',
            149 => 'Шоссе Энтузиастов',
            150 => 'Раменки (2016г.)',
            151 => 'Минская (2016г.)',
            152 => 'Рассказовка (2017г.)',
            153 => 'Терешково',
            154 => 'Алтуфьево',
            155 => 'Аннино',
            156 => 'Бибирево',
            157 => 'Боровицкая',
            158 => 'Бульвар Дмитрия Донского',
            159 => 'Владыкино',
            160 => 'Дмитровская',
            161 => 'Менделеевская',
            162 => 'Нагатинская',
            163 => 'Нагорная',
            164 => 'Нахимовский проспект',
            165 => 'Отрадное',
            166 => 'Петровско-Разумовская',
            167 => 'Полянка',
            168 => 'Пражская',
            169 => 'Савеловская',
            170 => 'Севастопольская',
            171 => 'Серпуховская',
            172 => 'Тимирязевская',
            173 => 'Тульская',
            174 => 'Улица Академика Янгеля',
            175 => 'Цветной бульвар',
            176 => 'Чертановская',
            177 => 'Чеховская',
            178 => 'Южная',
            179 => 'Варшавская',
            180 => 'Каховская',
            181 => 'Битцевский парк',
            182 => 'Бульвар адмирала Ушакова',
            183 => 'Бунинская аллея',
            184 => 'Улица Горчакова',
            185 => 'Улица Скобелевская',
            186 => 'Улица Старокачаловская',
            187 => 'Октябрьская (КРЛ)',
            188 => 'Аэропорт',
            189 => 'Киевская (АПЛ)',
            190 => 'Кунцевская (АПЛ)',
            191 => 'Электрозаводская',
            192 => 'Кутузовская',
            193 => 'Парк культуры (КЛ)',
            194 => 'Комсомольская (КЛ)',
            195 => 'Павелецкая (КЛ)',
            196 => 'Киевская (Кольцевая линия)',
            197 => 'Белорусская (КЛ)',
            198 => 'Курская (КЛ)',
            199 => 'Киевская (КЛ)',
            200 => 'Проспект Мира (КРЛ)',
            201 => 'Китай-город (ТКЛ)',
            202 => 'Лермонтовский проспект',
            203 => 'Таганская (ТКЛ)',
            204 => 'Волхонка (2019г.)',
            205 => 'Кутузовский проспект (2019г.)',
            206 => 'Плющиха (2019г.)',
            207 => 'Третьяковская (КСЛ)',
            208 => 'Крестьянская застава',
            209 => 'Каширская (КЛ)',
            210 => 'Лесопарковая',
            211 => 'Парк Победы (КСЛ)',
            212 => 'Некрасовка (2017г.)',
            213 => 'Шелепиха (2017г.)',
            214 => 'Аминьевское Шоссе (2020г.)',
            215 => 'Ломоносовский проспект (2016г.)',
            216 => 'Улица Народного Ополчения (2017г.)',
            217 => 'Хорошевская (2017г.)',
            218 => 'Ходынское поле (2017г.)',
            219 => 'Котельники',
            220 => 'Мичуринский проспект (2017г.)',
            221 => 'Мичуринский проспект (2020г.)',
            222 => 'Нижняя Масловка (2017г.)',
            223 => 'Тропарево',
            224 => 'Ховрино',
            225 => 'Петровский парк',
            226 => 'Селигерская',
            227 => 'Спартак',
            228 => 'Очаково (2017г.)',
            229 => 'Очаково (2016 г.)',
            230 => 'Технопарк',
            231 => 'Улица Дмитриевского (2017г.)',
            232 => 'Солнцево (2017г.)',
            233 => 'Рассказовка (2017г.)',
            234 => 'Боровское шоссе (2017г.)',
            235 => 'Терешково (2017г.)',
        ];
        
        $this->load->model('Metro_Station_Model');
        
        foreach ($list as $id => $name){
            $this->Metro_Station_Model->update_by_primary_key($id, ['name' => $name]);
        }
    }

}
