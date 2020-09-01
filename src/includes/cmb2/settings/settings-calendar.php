<?php
add_action('cmb2_admin_init', 'iande_settings');

function iande_settings()
{

    $args = [
        'id'           => 'iande_options_page',
        'title'        => __('Iandé', 'iande'),
        'object_types' => ['options-page'],
        'option_key'   => 'iande',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Iandé', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    $iande_calendar_options = new_cmb2_box($args);

    $iande_calendar_options->add_field([
        'name' => '',
        'id'   => 'calendar_appointments',
        'type' => 'calendar_appointments'
    ]);
    
}