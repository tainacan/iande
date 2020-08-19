<?php

add_action('cmb2_admin_init', 'iande_register_theme_options_metabox');

function iande_register_theme_options_metabox() {

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
        'text' => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    $iande_main_options->add_field([
        'name'    => __('Escolaridade', 'iande'),
        'id'      => 'iande_schooling_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo as escolaridades disponíveis para as instituições', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Escolaridade', 'iande'),
        'id'         => 'institution_schooling',
        'type'       => 'text',
        'repeatable' => true,
        'text' => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    $iande_main_options->add_field([
        'name'    => __('Relação do Responsável com a Instituição', 'iande'),
        'id'      => 'iande_responsible_role_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo as relações disponíveis para as instituições', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Relação do Responsável com a Instituição', 'iande'),
        'id'         => 'institution_responsible_role',
        'type'       => 'text',
        'repeatable' => true,
        'text' => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    // Deficiências
    $iande_main_options->add_field([
        'name'    => __('Vocabulário de Deficiências', 'iande'),
        'id'      => 'iande_deficiency_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo o vocabulário de deficiências atendidas', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Deficiências', 'iande'),
        'id'         => 'institution_deficiency',
        'type'       => 'text',
        'repeatable' => true,
        'text' => array(
            'add_row_text' => __('Adicionar nova opção', 'iande')
        )
    ]);

    // Idiomas
    $iande_main_options->add_field([
        'name'    => __('Vocabulário de Idiomas', 'iande'),
        'id'      => 'iande_language_title',
        'type'    => 'title',
        'desc'    => __('Gerencie abaixo o vocabulário de idiomas adicionais atendidos', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Idiomas adicionais', 'iande'),
        'id'         => 'institution_language',
        'type'       => 'text',
        'repeatable' => true,
        'text' => array(
            'add_row_text' => __('Adicionar nova opção', 'iande')
        )
    ]);

}

/**
 * Expõe as configuracoes/dados no frontend
 */
add_action('wp_enqueue_scripts', 'iande_institution_settings');

function iande_institution_settings() {

    $site_name            = get_bloginfo( 'name' );
    $profiles             = iande_get_option('institution_profile', []);
    $responsible_role     = iande_get_option('institution_responsible_role', []);
    $deficiency           = iande_get_option('institution_deficiency',[]);
    $institution_language = iande_get_option('institution_language', []);

    wp_localize_script(
        'iande',
        'IandeSettings',
        [
            'site_name'        => $site_name,
            'profiles'         => $profiles,
            'responsible_role' => $responsible_role,
            'deficiency'       => $deficiency,
            'language'         => $institution_language
        ]
    );
    
}
