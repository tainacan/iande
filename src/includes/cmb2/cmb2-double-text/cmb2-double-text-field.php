<?php

/**
 * Render Double Text Field
 */
function cmb2_render_double_text_field_callback($field, $value, $object_id, $object_type, $field_type)
{

    // get the ID field
    $field_id  = $field->args['id'];
    $_field_id = '_' . $field_id;

    // get the specific name to each field
    $name_1 = isset($field->args['options']['name_1']) ? $field->args['options']['name_1'] : '';
    $name_2 = isset($field->args['options']['name_2']) ? $field->args['options']['name_2'] : '';

    // make sure we specify each part of the value we need.
    $value = wp_parse_args($value, array(
        $field_id. '_1' => '',
        $field_id. '_2' => ''
    ));
?>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id($_field_id . '_1'); ?>"><?php echo esc_attr($name_1) ?></label></p>
        <?php echo $field_type->input(array(
            'name'  => $field_type->_name('[' . $field_id . '_1]'),
            'id'    => $field_type->_id($_field_id . '_1'),
            'value' => $value[$field_id . '_1'],
            'desc'  => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id($_field_id . '_2'); ?>'"><?php echo esc_attr($name_2) ?></label></p>
        <?php echo $field_type->input(array(
            'name'  => $field_type->_name('[' . $field_id . '_2]'),
            'id'    => $field_type->_id($_field_id . '_2'),
            'value' => $value[$field_id . '_2'],
            'desc'  => ''
        )); ?>
    </div>
    <br class="clear">

<?php
    echo $field_type->_desc(true);
}
add_filter('cmb2_render_double_text', 'cmb2_render_double_text_field_callback', 10, 5);

/**
 * Sanitize Double Text Field
 */
function cmb2_sanitize_double_text_field($check, $meta_value, $object_id, $field_args, $sanitize_object)
{

    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $value) {
        $meta_value[$key] = array_map('sanitize_text_field', $value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return $new_meta_value;

}
add_filter('cmb2_sanitize_double_text', 'cmb2_sanitize_double_text_field', 10, 5);

/**
 * Escape Double Text Field
 */
function cmb2_types_esc_double_text_field($check, $meta_value, $field_args, $field_object)
{

    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $val) {
        $meta_value[$key] = array_map('esc_attr', $val);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return $new_meta_value;

}
add_filter('cmb2_types_esc_double_text', 'cmb2_types_esc_double_text_field', 10, 4);