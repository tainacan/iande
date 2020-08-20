<?php

add_action('cmb2_admin_init', 'iande_register_theme_options_metabox');

function iande_register_theme_options_metabox() {

    /**
     * Registers main options page menu item and form.
     */
    $args = [
        'id'           => 'iande_main_options_page',
        'title'        => __('Iandé', 'iande'),
        'object_types' => ['options-page'],
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
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
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
    ]);

    /**
     * Hour settings tab
     */
    $args = [
        'id'           => 'iande_schedules_options_page',
        'title'        =>  __('Configuração de Horários', 'iande'),
        'object_types' => ['options-page'],
        'option_key'   => 'iande_schedules',
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_institution',
        'tab_title'    => __('Horários', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    // 'tab_group' property is supported in > 2.4.0.
    if (version_compare(CMB2_VERSION, '2.4.0')) {
        $args['display_cb'] = 'iande_options_display_with_tabs';
    }

    $iande_schedules_options = new_cmb2_box($args);

    /**
     * Monday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Terça-feira', 'iande'),
        'id'   => 'monday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'monday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
        	'data-timepicker' => json_encode( [
        		'timeOnlyTitle' => __( 'Escolha o horário', 'iande' ),
        		'timeFormat'    => 'HH:mm',
        		'stepMinute'    => 10,
             ] ),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Tuesday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Terça-feira', 'iande'),
        'id'   => 'tuesday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'tuesday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Wednesday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Quarta-feira', 'iande'),
        'id'   => 'wednesday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'wednesday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Thursday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Quinta-feira', 'iande'),
        'id'   => 'thursday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'thursday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Friday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Sexta-feira', 'iande'),
        'id'   => 'friday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'friday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Saturday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Sábado', 'iande'),
        'id'   => 'saturday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'saturday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);

    /**
     * Sunday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Domingo', 'iande'),
        'id'   => 'sunday_title',
        'type' => 'title'
    ]);
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'id'          => 'sunday',
        'type'        => 'group',
        'options'     => [
            'group_title'   => __('Horário {#}', 'iande'),
            'add_button'    => __('Adicionar novo horário', 'iande'),
            'remove_button' => __('Remover horário', 'iande'),
            'closed'        => true
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('De', 'iande'),
        'id'   => 'from',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
    ]);
    $iande_schedules_options->add_group_field($iande_schedules_fields, [
        'name' => __('Até', 'iande'),
        'id'   => 'to',
        'type' => 'text_time',
        'time_format' => 'H:i',
        'attributes'  => [
            'data-timepicker' => json_encode([
                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                'timeFormat'    => 'HH:mm',
                'stepMinute'    => 10,
            ]),
        ],
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
    $schedules        = get_option('iande_schedules', []);

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
            'ageRanges'        => $age_range,
            'schedules'        => $schedules

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