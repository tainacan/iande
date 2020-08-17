<?php

add_action('cmb2_admin_init', 'iande_register_theme_options_metabox');

function iande_register_theme_options_metabox()
{

    /**
     * Registers main options page menu item and form.
     */
    $args = [
        'id'           => 'iande_main_options_page',
        'title'        => __('Iandé', 'iande'),
        'object_types' => array('options-page'),
        'option_key'   => 'iande',
        'tab_group'    => 'iande_institution',
        'tab_title'    => __('Instituição', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    // 'tab_group' property is supported in > 2.4.0.
    if (version_compare(CMB2_VERSION, '2.4.0')) {
        $args['display_cb'] = 'iande_options_display_with_tabs';
    }

    $iande_main_options = new_cmb2_box($args);

    $iande_main_options->add_field([
        'name'    => __('Perfil da insituição', 'iande'),
        'id'      => 'iande_profile_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo os perfis disponíveis para as instituições', 'iande')
    ]);

    $iande_main_options->add_field([
        'name'       => __('Perfil', 'iande'),
        'id'         => 'institution_profile',
        'type'       => 'text',
        'repeatable' => true,
    ]);

    
}

/**
 * Expõe as configuracoes do plugin no frontend
 */
add_action('wp_enqueue_scripts', 'iande_institution_settings');

function iande_institution_settings()
{

    /**
     * Expõe os perfis para instituições no frontend
     */
    $profiles = iande_get_option('institution_profile');

    if ( is_array($profiles) && !empty($profiles) ) {
        
        // Localize profiles.
        wp_localize_script(
            'iande',
            'IandeAdminSettings',
            $profiles
        );

    }

}