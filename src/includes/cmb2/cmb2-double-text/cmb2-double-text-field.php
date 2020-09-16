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
        <p><label for="<?php echo $field_type->_id($_field_id . '_1'); ?>"><?= $name_1 ?></label></p>
        <?php echo $field_type->input(array(
            'name'  => $field_type->_name('[' . $field_id . '_1]'),
            'id'    => $field_type->_id($_field_id . '_1'),
            'value' => $value[$field_id . '_1'],
            'desc'  => '',
        )); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id($_field_id . '_2'); ?>'"><?= $name_2 ?></label></p>
        <?php echo $field_type->input(array(
            'name'  => $field_type->_name('[' . $field_id . '_2]'),
            'id'    => $field_type->_id($_field_id . '_2'),
            'value' => $value[$field_id . '_2'],
            'desc'  => '',
        )); ?>
    </div>
    <br class="clear">

<?php
    echo $field_type->_desc(true);
}
add_filter('cmb2_render_double_text', 'cmb2_render_double_text_field_callback', 10, 5);
