<?php
add_action('cmb2_admin_init', 'iande_settings');

function iande_settings()
{

    $args = [
        'id'           => 'iande_options_page',
        'title'        => __('Iandé', 'iande'),
        'object_types' => ['options-page'],
        'capability'   => 'manage_iande_options',
        'option_key'   => 'iande',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Iandé', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    $iande_initial_options = new_cmb2_box($args);

}