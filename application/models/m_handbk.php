<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Модель работы со справочниками
 * form elements
 * @todo refactoring
 */
class M_handbk extends MY_Model {
    protected $table = 'handbks';
    protected $primary_key = 'handbk_id';
    public $zone_id;
    // существующие справочники
    // @todo хранить в базе
    public $handbks = array(
        'geo_area' => 'МО: Район / Гор. округ',
        'populated_locality' => 'МО: Населенный пункт',
        'populated_locality_type' => 'Тип населенного пункта',
        'district' => 'Москва: Округ',
        'square' => 'Москва: Район',
        'metro_line' => 'Линии метро',
        'metro_station' => 'Станции метро',
        'proportion' => 'Пропорции',
        'file_catigories' => 'Категории',
        'tag' => 'Теги',
        'ghandbks' => array('name' => 'Общие справочники', 'url' => '/admin/ghandbks/'),
        'registry' => array('name' => 'Реестр', 'url' => '/admin/registry/'),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('Geo');
        $this->load_model('Geo_Area_Model');
        $this->zone_id = $this->Geo->zone['MOS']->zone_id;
        $handbks = $this->get_list();

        foreach ($handbks as $it)
            if ($it['adm_url'])
                $this->handbks[$it['table']] = array('name' => $it['name'], 'url' => $it['adm_url']);
            elseif ($it['table'] !== 'registry')
                $this->handbks[$it['table']] = $it['name'];
    }

    public function form_geo_area($content) {
        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);
        // select zone_id        
        $form .= $this->form_el_zone($content);
        // select geo_direction_id
        $form .= $this->form_el_geo_direction($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_populated_locality($content) {
        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);
        // select zone_id        
        $form .= $this->form_el_zone($content);
        // select geo_direction_id
        $form .= $this->form_el_geo_direction($content);
        $form .= $this->form_el_geo_area($content);
        $form .= $this->form_el_populated_locality_type($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_populated_locality_type($content) {
        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);
        $form .= $this->form_el_short_name($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_district($content) {
        $form = form_open('', array('role' => 'form'), array('populated_locality_id' => 1));
        // name
        $form .= $this->form_el_name($content);
        $form .= $this->form_el_short_name($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_square($content) {
        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);
        $form .= $this->form_el_district($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_metro_line($content) {
        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);
        $form .= $this->form_el_color($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_metro_station($content) {

        $form = form_open('', array('role' => 'form'));
        // name
        $form .= $this->form_el_name($content);

        // define metro_line element
        {
            if (array_get($content, 'metro_station_id')) {
                $this->load->model('Metro_Station_Model');
                $lines = $this->Metro_Station_Model->get_station_lines($content['metro_station_id']);
                $count_lines = count($lines);
            }

            $metro_line_tpl_vars = [
                'options' => array_merge([['metro_line_id' => '', 'name' => 'Не выбрано']], $this->Geo->get_metro_line()),
                'value' => 'metro_line_id',
                'text' => 'name',
                'name' => 'metro_line_id[]',
                'minus' => FALSE,
            ];

            if (isset($lines) && $lines) {
                $metro_lines_selects = '';
                foreach ($lines as $k => $l) {
                    $metro_lines_selects .= $this->load->view('admin/forms/elements/select_add', array_merge($metro_line_tpl_vars, [
                        'current_value' => array_get($l, 'metro_line_id'),
                        'minus' => $k + 1 === $count_lines ? false : TRUE,
                            ]), TRUE);
                }
            } else {
                $metro_lines_selects = $this->load->view('admin/forms/elements/select_add', $metro_line_tpl_vars, TRUE);
            }

            $form .= form_group($metro_lines_selects, $this->handbks['metro_line']);
        }

        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_proportions($content) {
        $form = form_open('', array('role' => 'form'));
        $form .= $this->form_el_x($content);
        $form .= $this->form_el_y($content);
        // status
        $form .= $this->form_el_status($content);
        // submit
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    public function form_el_zone($get, $zone = false, $zone_id = false) {
        // select zone_id
        if (!$zone)
            $zone = pluck_key_value(objects_to_arrays($this->Geo->zone), 'zone_id', 'name');

        unset($zone['2761']); // MSK

        if (!$zone_id)
            $zone_id = element('zone_id', $get, key($zone));
        $this->zone_id = $zone_id;
        $select_zone = form_dropdown('zone_id', $zone, $zone_id, 'class="form-control"');
        return form_group($select_zone, 'Регион', 'zone_id');
    }

    public function form_el_geo_direction($get) {
        // select geo_direction_id
        $geo_direction = array('0' => 'Не выбрано');
        $geo_direction = array_merge($geo_direction, pluck_key_value(objects_to_arrays($this->Geo->get_directions()), 'geo_direction_id', 'name'));
        $geo_direction_id = element('geo_direction_id', $get, 0);
        $select_geo_direction = form_dropdown('geo_direction_id', $geo_direction, $geo_direction_id, 'class="form-control"');
        return form_group($select_geo_direction, 'Направление', 'geo_direction_id');
    }

    public function form_el_geo_area($get, $zone_id = FALSE, $geo_direction_id = FALSE) {
        $zone_id = !$zone_id ? $this->zone_id : $zone_id;
        // select geo_area_id
        $geo_area = array('0' => 'Не выбрано');
        $geo_area = array_merge($geo_area, pluck_key_value($this->Geo->get_geo_area($zone_id, $geo_direction_id), 'geo_area_id', 'name'));
        $geo_area_id = element('geo_area_id', $get, 0);
        $select_geo_area = form_dropdown('geo_area_id', $geo_area, $geo_area_id, 'class="form-control"');
        return form_group($select_geo_area, 'Район / Гор. округ', 'geo_area_id');
    }

    public function form_el_populated_locality_type($get) {
        $hb = str_replace('form_el_', '', __FUNCTION__);
        // select populated_locality_type
        $data = array('0' => 'Не выбрано');
        $data = array_merge($data, pluck_key_value($this->Geo->get_populated_locality_type(), $hb . '_id', 'name'));
        $data_id = element($hb . '_id', $get, 0);
        $select = form_dropdown($hb . '_id', $data, $data_id, 'class="form-control"');
        return form_group($select, $this->handbks[$hb], $hb . '_id');
    }

    public function form_el_district($get, $populated_locality_id = 1) {
        $hb = str_replace('form_el_', '', __FUNCTION__);
        // select 
        $data = array('0' => 'Не выбрано');
        $data = array_merge($data, pluck_key_value($this->Geo->get_districts_list($populated_locality_id), $hb . '_id', 'name'));
        $data_id = element($hb . '_id', $get, 0);
        $select = form_dropdown($hb . '_id', $data, $data_id, 'class="form-control"');
        return form_group($select, $this->handbks[$hb], $hb . '_id');
    }

    public function form_el_metro_line($get, $required = FALSE) {
        $hb = str_replace('form_el_', '', __FUNCTION__);
        // select 
        $data = array('0' => 'Не выбрано');
        $data = array_merge($data, pluck_key_value($this->Geo->get_metro_line(), $hb . '_id', 'name'));
        $data_id = element($hb . '_id', $get, 0);
        $select = form_dropdown($hb . '_id', $data, $data_id, 'class="form-control"');
        $required = !$required ? array() : array('required' => TRUE);
        return form_group($select, $this->handbks[$hb], $hb . '_id', $required);
    }

    public function form_el_status($get, $empty_value = FALSE) {
        $data = array();
        $default_value = 1;
        if ($empty_value) {
            $data[''] = 'Не выбрано';
            $default_value = '';
        }  
                
        return $this->load->view($this->template_dir . 'elements/status_radio', [
            'status_list' => $this->Geo_Area_Model->get_status_list(),
            'data' => $get,
        ], TRUE);
        
        return form_group(form_dropdown('status', $data + $this->Geo->status, element('status', $get, $default_value), 'class="form-control"'), 'Статус', 'status');
    }

    public function form_el_submit($title = 'Сохранить', $color_class = 'btn-success') {
        return form_decorate_element(
                form_decorate_element(form_submit(array('class' => 'btn ' . $color_class), $title), array('class' => ''))
                , array('class' => 'form-group'));
    }

    public function form_el_name($content) {
        return form_group(
                form_input(array(
            'name' => 'name',
            'id' => 'name',
            'class' => 'form-control',
            'value' => element('name', $content, ''),
                )), 'Название', 'name', array('required' => TRUE));
    }

    public function form_el_x($content) {
        return form_group(
                form_input(array(
            'name' => 'x',
            'id' => 'x',
            'class' => 'form-control',
            'value' => element('x', $content, ''),
                )), 'Ширина', 'x', array('required' => TRUE));
    }

    public function form_el_y($content) {
        return form_group(
                form_input(array(
            'name' => 'y',
            'id' => 'y',
            'class' => 'form-control',
            'value' => element('y', $content, ''),
                )), 'Высота', 'y', array('required' => TRUE));
    }

    public function form_el_short_name($content) {
        return form_group(
                form_input(array(
            'name' => 'short_name',
            'id' => 'short_name',
            'class' => 'form-control',
            'value' => element('short_name', $content, ''),
                )), 'Сокращение', 'short_name', array('required' => TRUE));
    }

    public function form_el_color($content) {
        return form_group(
                form_input_group(
                        form_input(array(
                            'name' => 'color',
                            'class' => 'form-control',
                            'value' => element('color', $content, ''),
                        )) .
                        '<span class="input-group-addon"><i></i></span>'
                        , 'set_color'), 'Цвет', 'color', array('required' => TRUE)
        );
    }

    public function form_colorpicker() {
        return '<script>$(".set_color").colorpicker();</script>';
    }

    /**
     * Check order (sort) direction
     * @param string $direction
     * @return string
     */
    public function check_order_direction($direction) {
        $variants = array('ASC', 'DESC');
        $direction = strtoupper($direction);
        return in_array($direction, $variants) ? $direction : $variants[0];
    }

    public function form_file_catigory($post) {
        $form = form_open('', array('role' => 'form'));
        $form .= $this->form_el_name($post);
        $form .= form_group(
                form_input(array('name' => 'prefix', 'class' => 'form-control', 'value' => element('prefix', $post, ''),)), 'Префикс', 'prefix', array('required' => TRUE));
        $form .= form_group(
                form_input(array('name' => 'uri', 'class' => 'form-control', 'value' => element('uri', $post, ''),)), 'Ссылка', 'uri', array('required' => TRUE));
        $form .= form_group(
                form_input(array('name' => 'uri_adm', 'class' => 'form-control', 'value' => element('uri_adm', $post, ''),)), 'Ссылка <br>в админке', 'uri_adm', array('required' => TRUE));
        $form .= $this->form_el_status($post);
        $form .= $this->form_el_submit();
        $form .= form_close();

        return $form;
    }

    public function form_tag($post) {
        $form = form_open('', array('role' => 'form'));
        $form .= $this->form_el_name($post);
        $form .= $this->form_el_submit();
        $form .= form_close();
        return $form;
    }

    /**
     * 
     * @param array $content
     * @param string $autocomplete_type
     * @return string
     */
    public function form_el_name_like($content, $autocomplete_type = '') {
        return form_group(
                form_input(array(
            'name' => 'name_like',
            'id' => 'name_like',
            'class' => 'form-control',
            'value' => element('name_like', $content, ''),
            'data-autocomplete' => $autocomplete_type,
            'placeholder' => $label = 'Поиск по названию или id',
            'autocomplete' => 'off'
                )), $label, 'name_like');
    }

}
