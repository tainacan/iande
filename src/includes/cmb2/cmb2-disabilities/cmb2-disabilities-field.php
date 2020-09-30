<?php

/**
 * Render Disabilities Field
 */
function cmb2_render_disabilities_field_callback($field, $value, $object_id, $object_type, $field_type)
{

    // get the ID field
    $field_id  = $field->args['id'];

    // make sure we specify each part of the value we need.
    $value = wp_parse_args($value, array(
        $field_id . '_type' => '',
        $field_id . '_other' => '',
        $field_id . '_qty' => ''
    ));

    // get disabilities from settings
    $disabilities = cmb2_get_option('iande_institution', 'institution_deficiency', []);

    if (is_array($disabilities)) {

        $deficiencies_options = '';
        $deficiencies_options .= '<option value="" selected>'. __('Selecione uma Deficiência', 'iande') .'</option>';

        foreach ($disabilities as $deficiency) {
            if (!empty($deficiency)) {
                
                $selected = ($value['disabilities_type'] == $deficiency) ? 'selected' : '';
                $deficiencies_options .= '<option value="' . $deficiency . '" ' . $selected . '>' . $deficiency . '</option>';

            }
        }
    }

?>

    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_disabilities_type'); ?>'"></label></p>
        <?php echo $field_type->select(array(
            'name'    => $field_type->_name('[disabilities_type]'),
            'id'      => $field_type->_id('_disabilities_type'),
            'value'   => $value['disabilities_type'],
            'options' => $deficiencies_options,
            'desc'    => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_disabilities_other'); ?>'"></label></p>
        <?php echo $field_type->input(array(
            'name'        => $field_type->_name('[' . $field_id . '_other]'),
            'id'          => $field_type->_id('_disabilities_other'),
            'value'       => $value['disabilities_other'],
            'placeholder' => __('Outro', 'iande'),
            'desc'        => ''
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id('_disabilities_qty'); ?>'"></label></p>
        <?php echo $field_type->input(array(
            'name'        => $field_type->_name('[disabilities_qty]'),
            'id'          => $field_type->_id('_disabilities_qty'),
            'value'       => $value['disabilities_qty'],
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
        $meta_value[$key] = array_map('sanitize_text_field', $value);
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
        $meta_value[$key] = array_map('esc_attr', $value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return array_filter($new_meta_value);

}
add_filter('cmb2_types_esc_disabilities', 'cmb2_types_esc_disabilities_field', 10, 4);
