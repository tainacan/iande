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

    // Perfil
    $iande_main_options->add_field([
        'name' => __('Perfil da insituição', 'iande'),
        'id'   => 'iande_profile_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo os perfis disponíveis para as instituições', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Perfil', 'iande'),
        'id'         => 'institution_profile',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    // Escolaridade
    $iande_main_options->add_field([
        'name' => __('Escolaridade', 'iande'),
        'id'   => 'iande_schooling_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo as escolaridades disponíveis para as instituições', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Escolaridade', 'iande'),
        'id'         => 'institution_schooling',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    // Relação do Responsável
    $iande_main_options->add_field([
        'name' => __('Relação do Responsável com a Instituição', 'iande'),
        'id'   => 'iande_responsible_role_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo as relações disponíveis para as instituições', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Relação do Responsável com a Instituição', 'iande'),
        'id'         => 'institution_responsible_role',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ),
    ]);

    // Deficiências
    $iande_main_options->add_field([
        'name' => __('Vocabulário de Deficiências', 'iande'),
        'id'   => 'iande_deficiency_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de deficiências atendidas', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Deficiências', 'iande'),
        'id'         => 'institution_deficiency',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande')
        )
    ]);

    // Idiomas
    $iande_main_options->add_field([
        'name' => __('Vocabulário de Idiomas', 'iande'),
        'id'   => 'iande_language_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de idiomas adicionais atendidos', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Idiomas adicionais', 'iande'),
        'id'         => 'institution_language',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande')
        )
    ]);

    // Faixa Etária
    $iande_main_options->add_field([
        'name' => __('Vocabulário de Faixas Etárias', 'iande'),
        'id'   => 'iande_age_range_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de faixas etárias atendidas', 'iande')
    ]);
    $iande_main_options->add_field([
        'name'       => __('Faixas Etárias', 'iande'),
        'id'         => 'institution_age_range',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => array(
            'add_row_text' => __('Adicionar nova opção', 'iande')
        )
    ]);

}

/**
 * Expõe as configuracoes/dados no frontend
 */
add_action('wp_enqueue_scripts', 'iande_institution_settings');

function iande_institution_settings() {

    $site_name        = get_bloginfo('name');
    $site_url         = get_bloginfo('url');
    $iande_url        = get_site_url(null, '/iande');
    $profiles         = iande_get_option('institution_profile', []);
    $responsible_role = iande_get_option('institution_responsible_role', []);
    $deficiency       = iande_get_option('institution_deficiency',[]);
    $language         = iande_get_option('institution_language', []);
    $age_range        = iande_get_option('institution_age_range', []);

    wp_localize_script(
        'iande',
        'IandeSettings',
        [
            'siteName'         => $site_name,
            'siteUrl'          => $site_url,
            'iandeUrl'         => $iande_url,
            'profiles'         => $profiles,
            'responsibleRoles' => $responsible_role,
            'deficiencies'     => $deficiency,
            'languages'        => $language,
            'ageRanges'        => $age_range

        ]
    );

}

/**
 * Define os valores padrões quando o plugin é ativado
 * @see /includes/init.php
 */
function iande_cmb2_settings_init() {

    /**
     * Perfil
     */
    $institution_profile_default = [
        'Escola estadual',
        'Escola municipal',
        'Escola federal',
        'Escola privada',
        'Universidade pública',
        'Universidade/faculdade privada',
        'ONG',
        'Agência turismo',
        'Empresa',
        'Outros'
    ];
    $institution_profile = iande_get_option('institution_profile');

    if (is_array($institution_profile)) {
        $merge = array_merge($institution_profile_default, $institution_profile);
        cmb2_update_option('iande', 'institution_profile', array_unique($merge));
    } else {
        cmb2_update_option('iande', 'institution_profile', $institution_profile_default);
    }

    /**
     * Escolaridade
     */
    $institution_schooling_default = [
        'Educação infantil',
        'Ensino fundamental I',
        'Ensino fundamental II',
        'Ensino médio',
        'Ensino técnico',
        'EJA | MOVA',
        'Ensino superior',
        'Turma mista'
    ];
    $institution_schooling = iande_get_option('institution_schooling');

    if (is_array($institution_schooling)) {
        $merge = array_merge($institution_schooling_default, $institution_schooling);
        cmb2_update_option('iande', 'institution_schooling', array_unique($merge));
    } else {
        cmb2_update_option('iande', 'institution_schooling', $institution_schooling_default);
    }

    /**
     * Relação do Responsável
     */
    $institution_responsible_role_default = [
        'Professor',
        'Orientador',
        'Coordenador',
        'Diretor',
        'Guia de turismo',
        'Outros'
    ];
    $institution_responsible_role = iande_get_option('institution_responsible_role');

    if (is_array($institution_responsible_role)) {
        $merge = array_merge($institution_responsible_role_default, $institution_responsible_role);
        cmb2_update_option('iande', 'institution_responsible_role', array_unique($merge));
    } else {
        cmb2_update_option('iande', 'institution_responsible_role', $institution_responsible_role_default);
    }

    /**
     * Faixa Etária
     */
    $institution_age_range_default = [
        'até 4 anos',
        'de 5 a 9 anos',
        'De 10 a 14 anos',
        'De 15 a 19 anos',
        'De 20 a 24 anos',
        'De 25 a 39 anos',
        'De 40 a 59 anos',
        'Acima 60 anos',
        'Grupo misto',
    ];
    $institution_age_range = iande_get_option('institution_age_range');

    if (is_array($institution_age_range)) {
        $merge = array_merge($institution_age_range_default, $institution_age_range);
        cmb2_update_option('iande', 'institution_age_range', array_unique($merge));
    } else {
        cmb2_update_option('iande', 'institution_age_range', $institution_age_range_default);
    }

}