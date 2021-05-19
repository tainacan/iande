<?php

/**
 * Render Disabilities Field
 */
function cmb2_render_disabilities_field_callback($field, $value, $object_id, $object_type, $field_type)
{

    // get the ID field
    $field_id  = $field->args['id'];

    $array_value = (array) $value;

    $type  = $field_id . '_type';
    $other = $field_id . '_other';
    $count = $field_id . '_count';

    $_type  = '_' . $field_id . '_type';
    $_other = '_' . $field_id . '_other';
    $_count = '_' . $field_id . '_count';

    // make sure we specify each part of the value we need.
    $value = wp_parse_args($array_value, [
        $type  => '',
        $other => '',
        $count => ''
    ]);

    // get disabilities from settings
    $disabilities = cmb2_get_option('iande_institution', 'institution_deficiency', []);

    if (is_array($disabilities)) {

        $deficiencies_options = '';
        $deficiencies_options .= '<option value="" selected>'. __('Selecione uma Deficiência', 'iande') .'</option>';

        foreach ($disabilities as $deficiency) {
            if (!empty($deficiency)) {

                $selected = ($value[$type] == $deficiency) ? 'selected' : '';
                $deficiencies_options .= '<option value="' . $deficiency . '" ' . $selected . '>' . $deficiency . '</option>';

            }
        }
    }

?>

    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id( $_type ); ?>'"></label></p>
        <?php echo $field_type->select(array(
            'name'    => $field_type->_name( '[' . $type . ']' ),
            'id'      => $field_type->_id( $_type ),
            'value'   => $value[$type],
            'options' => $deficiencies_options,
            'desc'    => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_disabilities_other'); ?>'"></label></p>
        <?php echo $field_type->input(array(
            'name'        => $field_type->_name('[' . $other . ']'),
            'id'          => $field_type->_id( $_other ),
            'value'       => $value[$other],
            'placeholder' => __('Outro', 'iande'),
            'desc'        => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_disabilities_count'); ?>'"></label></p>
        <?php echo $field_type->input(array(
            'name'        => $field_type->_name('[' . $count . ']'),
            'id'          => $field_type->_id( $_count ),
            'value'       => $value[$count],
            'placeholder' => __('Quantidade', 'iande'),
            'desc'        => ''
        )); ?>
    </div>
    <br class="clear">

<?php
    echo $field_type->_desc(true);
}
add_filter('cmb2_render_disabilities', 'cmb2_render_disabilities_field_callback', 10, 5);

/**
 * Sanitize Deficiency Field
 */
function cmb2_sanitize_disabilities_field($check, $meta_value, $object_id, $field_args, $sanitize_object)
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
add_filter('cmb2_sanitize_disabilities', 'cmb2_sanitize_disabilities_field', 10, 5);

/**
 * Escape Deficiency Field
 */
function cmb2_types_esc_disabilities_field($check, $meta_value, $field_args, $field_object)
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
add_filter('cmb2_types_esc_disabilities', 'cmb2_types_esc_disabilities_field', 10, 4);
