<?php

/**
 * Decorate elemen form (wrap DIV)
 * @param string $element
 * @param array $attributes
 * @return string
 */
function form_decorate_element($element, $attributes = '') {
    $attributes = _attributes_to_string($attributes);
    return '<div ' . $attributes . '>' . $element . '</div>';
}

/**
 * Form group horizontal (bootstrap)
 * @param string $element - html
 * @param string $label - label for form control
 * @param string $name - field name
 * @param array $params - params
 * @return string
 */
function form_group_horizontal($element, $label = '', $name = '', $params = array()) {

    if (element('required', $params, FALSE) === TRUE)
        $label = $label . ' <span class="text-danger">*</span>';

    return form_decorate_element(
            form_label($label, $name . '_label', array('for' => $name, 'class' => 'col-sm-2 control-label')) .
            form_decorate_element($element, array('class' => 'col-sm-10'))
            , array('class' => 'form-group'));
}

/**
 * Form group (bootstrap)
 * @param string $element - html
 * @param string $label - label for form control
 * @param string $name - field name
 * @param array $params - params
 * @return string
 */
function form_group($element, $label = '', $name = '', $params = array()) {

    if (element('required', $params, FALSE) === TRUE)
        $label = $label . ' <span class="text-danger">*</span>';

    return form_decorate_element(
            form_label($label, $name . '_label', array('for' => $name, 'class' => 'control-label')) .
            $element, array('class' => 'form-group'));
}

/**
 * Wrapp elements to Bootstrap input-group
 * @param string $elements
 * @param string $css_class
 * @return string
 */
function form_input_group($elements, $css_class) {
    return '<div class="input-group ' . $css_class . '">' . $elements . '</div>';
}
