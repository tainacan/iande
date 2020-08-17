<?php

require 'helpers.php';

add_action('cmb2_admin_init', 'iande_register_theme_options_metabox');

function iande_register_theme_options_metabox()
{

    /**
     * Registers main options page menu item and form.
     */
    $args = array(
        'id'           => 'iande_main_options_page',
        'title'        => __('Iandé', 'iande'),
        'object_types' => array('options-page'),
        'option_key'   => 'iande',
        'tab_group'    => 'iande_institution',
        'tab_title'    => __('Instituição', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    );

    // 'tab_group' property is supported in > 2.4.0.
    if (version_compare(CMB2_VERSION, '2.4.0')) {
        $args['display_cb'] = 'iande_options_display_with_tabs';
    }

    $main_options = new_cmb2_box($args);

    $main_options->add_field(array(
        'name'    => __('Perfil da insituição', 'iande'),
        'id'      => 'iande_profile_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo os perfis disponíveis para as instituições', 'iande')
    ));

    /**
     * Profile
     */
    $profile = $main_options->add_field(array(
        'id'          => 'institution_profile',
        'type'        => 'group',
        'repeatable'  => true,
        'options'     => array(
            'group_title'   => 'Perfil {#}',
            'add_button'    => __('Adicionar perfil', 'iande'),
            'remove_button' => __('Remover perfil', 'iande'),
            'closed'        => true,  // Repeater fields closed by default - neat & compact.
            //'sortable'    => true,  // Allow changing the order of repeated groups.
        ),
        'after_group' => 'iande_add_js_for_repeatable_titles'
    ));
    $main_options->add_group_field($profile, array(
        'name' => __('Perfil', 'iande'),
        'id'   => 'title',
        'type' => 'text',
    ));

    
}