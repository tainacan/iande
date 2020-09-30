<?php

/**
 * Render Languages Field
 */
function cmb2_render_languages_field_callback($field, $value, $object_id, $object_type, $field_type)
{

    // get the ID field
    $field_id  = $field->args['id'];

    $array_value = (array) $value;

    // make sure we specify each part of the value we need.
    $value = wp_parse_args($array_value, array(
        $field_id . '_name' => '',
        $field_id . '_other' => ''
    ));

    // get languages from settings
    $languages = cmb2_get_option('iande_institution', 'institution_language', []);

    if (is_array($languages)) {

        $languages_options = '';
        $languages_options .= '<option value="" selected>'. __('Selecione um Idioma', 'iande') .'</option>';

        foreach ($languages as $languages) {
            if (!empty($languages)) {
                
                $selected = ($value['languages_name'] == $languages) ? 'selected' : '';
                $languages_options .= '<option value="' . $languages . '" ' . $selected . '>' . $languages . '</option>';

            }
        }
    }

?>

    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_languages_name'); ?>'"></label></p>
        <?php echo $field_type->select(array(
            'name'    => $field_type->_name('[languages_name]'),
            'id'      => $field_type->_id('_languages_name'),
            'value'   => $value['languages_name'],
            'options' => $languages_options,
            'desc'    => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_languages_other'); ?>'"></label></p>
        <?php echo $field_type->input(array(
            'name'        => $field_type->_name('[' . $field_id . '_other]'),
            'id'          => $field_type->_id('_languages_other'),
            'value'       => $value['languages_other'],
            'placeholder' => __('Outro', 'iande'),
            'desc'        => ''
        )); ?>
    </div>
    <br class="clear">

<?php
    echo $field_type->_desc(true);
}
add_filter('cmb2_render_languages', 'cmb2_render_languages_field_callback', 10, 5);

/**
 * Sanitize Languages Field
 */
function cmb2_sanitize_languages_field($check, $meta_value, $object_id, $field_args, $sanitize_object)
{

    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $value) {
        $array_value      = (array) $value;
        $meta_value[$key] = array_map('sanitize_text_field', $array_value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return array_filter($new_meta_value);

}
add_filter('cmb2_sanitize_languages', 'cmb2_sanitize_languages_field', 10, 5);

/**
 * Escape Languages Field
 */
function cmb2_types_esc_languages_field($check, $meta_value, $field_args, $field_object)
{

    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $value) {
        $array_value      = (array) $value;
        $meta_value[$key] = array_map('esc_attr', $array_value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return array_filter($new_meta_value);

}
add_filter('cmb2_types_esc_languages', 'cmb2_types_esc_languages_field', 10, 4);
