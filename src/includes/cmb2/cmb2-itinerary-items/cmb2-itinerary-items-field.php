<?php

/**
 * Render itinerary_item field
 */
function cmb2_render_itinerary_items_field_callback ($field, $value, $object_id, $object_type, $field_type) {
    $field_id = $field->args['id'];

    $array_value = (array) $value;

    $item_id = $field_id . '_id';
    $item_description = $field_id . '_description';

    $_item_id = '_' . $item_id;
    $_item_description = '_' . $item_description;

    $value = wp_parse_args($array_value, [
        $item_id => '',
        $item_description => '',
    ]);
?>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id($_item_id) ?>"></label></p>
        <?php echo $field_type->input([
            'desc'        => '',
            'id'          => $field_type->_id($_item_id),
            'name'        => $field_type->_name('[' . $item_id . ']'),
            'placeholder' => __('Item', 'iande'),
            'type'        => 'number',
            'value'       => $value[$item_id],
        ]); ?>
    </div>
    <div class="alignleft">
        <p><label for="<?php echo $field_type->_id($_item_description) ?>"></label></p>
        <?php echo $field_type->textarea([
            'desc'  => '',
            'id'    => $field_type->_id($_item_description),
            'name'  => $field_type->_name('[' . $item_description . ']'),
            'rows'  => 3,
            'value' => $value[$item_description],
        ]); ?>
    </div>
    <br class="clear">
<?php
    echo $field_type->_desc(true);
}
add_filter('cmb2_render_itinerary_items', 'cmb2_render_itinerary_items_field_callback', 10, 5);

/**
 * Sanitize itinerary_item field
 */
function cmb2_sanitize_itinerary_items_field ($check, $meta_value, $object_id, $field_args, $sanitize_object) {
    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $value) {
        $array_value = (array) $value;
        $meta_value[$key] = array_map('sanitize_text_field', $array_value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return array_filter($new_meta_value);
}
add_filter('cmb2_sanitize_itinerary_items', 'cmb2_sanitize_itinerary_items_field', 10, 5);

/**
 * Escape itinerary_items field
 */
function cmb2_types_esc_itinerary_items_field ($check, $meta_value, $field_args, $field_object) {
    if (!is_array($meta_value) || !$field_args['repeatable']) {
        return $check;
    }

    foreach ($meta_value as $key => $value) {
        $array_value = (array) $value;
        $meta_value[$key] = array_map('esc_attr', $array_value);
    }

    $new_meta_value = array_map('array_filter', $meta_value);

    return array_filter($new_meta_value);
}
add_filter('cmb2_types_esc_itinerary_items', 'cmb2_types_esc_itinerary_items_field', 10, 4);
