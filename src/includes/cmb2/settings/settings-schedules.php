<?php

add_action('cmb2_admin_init', 'iande_settings_schedules');

function iande_settings_schedules() {

    /**
     * Configurações dos Agendamentos
     */
    $args = [
        'id'           => 'iande_schedules_options_page',
        'title'        =>  __('Agendamentos', 'iande'),
        'object_types' => ['options-page'],
        'option_key'   => 'iande_schedules_settings',
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Agendamentos', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    $iande_schedules_options = new_cmb2_box($args);

    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Duração da visita', 'iande'),
        'id'   => 'duration_title',
        'type' => 'title'
    ]);
    $iande_schedules_options->add_field([
        'name' => __('Duração (em minutos)', 'iande'),
        'description' => __('@todo adicionar descrição mais detalhada para esse campo', 'iande'),
        'id'   => 'duration',
        'type' => 'text_small'
    ]);

    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Tamanho dos grupos', 'iande'),
        'id'   => 'group_size_title',
        'type' => 'title'
    ]);
    $iande_schedules_options->add_field([
        'name' => __('Tamanho (máximo) dos grupos', 'iande'),
        'description' => __('@todo adicionar descrição mais detalhada para esse campo', 'iande'),
        'id'   => 'group_size',
        'type' => 'text_small'
    ]);

    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Quantidade de grupos por slot', 'iande'),
        'id'   => 'group_slot_title',
        'type' => 'title'
    ]);
    $iande_schedules_options->add_field([
        'name' => __('Quantidade (máxima) de grupos por slot', 'iande'),
        'description' => __('@todo adicionar descrição mais detalhada para esse campo', 'iande'),
        'id'   => 'group_slot',
        'type' => 'text_small'
    ]);

    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Grid', 'iande'),
        'id'   => 'grid_title',
        'type' => 'title'
    ]);
    $iande_schedules_options->add_field([
        'name' => __('Grid', 'iande'),
        'description' => __('@todo adicionar descrição mais detalhada para esse campo', 'iande'),
        'id'   => 'grid',
        'type' => 'text_small'
    ]);

    $args = [
        'id'           => 'iande_schedules_options_page',
        'title'        =>  __('Horários', 'iande'),
        'object_types' => ['options-page'],
        'option_key'   => 'iande_schedules',
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Horários', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    $iande_schedules_options = new_cmb2_box($args);

    /**
     * Monday
     */
    $iande_schedules_fields = $iande_schedules_options->add_field([
        'name' => __('Segunda-feira', 'iande'),
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
