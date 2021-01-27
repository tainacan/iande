<?php

add_action('cmb2_admin_init', 'iande_settings_appointments');

function iande_settings_appointments() {

    /**
     * Configurações dos Agendamentos
     */
    $args = [
        'id'           => 'iande_appointments_options_page',
        'object_types' => ['options-page'],
        'capability'   => 'manage_iande_options',
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_tabs',
        'option_key'   => 'iande_appointments_settings',
        'title'        => __('Agendamentos', 'iande'),
        'tab_title'    => __('Agendamentos', 'iande'),
        'save_button'  => __('Salvar opções', 'iande')
    ];

    $iande_appointments_options = new_cmb2_box($args);

    $iande_appointments_options->add_field([
        'name' => __('Objetivos da Visita', 'iande'),
        'id'   => 'appointment_purpose_title',
        'type' => 'title'
    ]);
    $iande_appointments_options->add_field([
        'name'       => __('Objetivos', 'iande'),
        'id'         => 'appointment_purpose',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
    ]);

}
