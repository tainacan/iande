<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_appointment');

/**
 * Registra o Post Type Appointment
 */
function register_post_type_appointment()
{
    $appointment_labels = [
        'name'               => _x('Agendamentos', 'post type general name', 'iande'),
        'singular_name'      => _x('Agendamento', 'post type singular name', 'iande'),
        'menu_name'          => _x('Agendamentos', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Agendamento', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar novo', 'agendamento', 'iande'),
        'add_new_item'       => __('Adicionar Novo Agendamento', 'iande'),
        'new_item'           => __('Novo Agendamento', 'iande'),
        'edit_item'          => __('Editar Agendamento', 'iande'),
        'view_item'          => __('Ver Agendamento', 'iande'),
        'all_items'          => __('Todos os Agendamentos', 'iande'),
        'search_items'       => __('Buscar Agendamentos', 'iande'),
        'parent_item_colon'  => __('Agendamentos Pais:', 'iande'),
        'not_found'          => __('Nenhum agendamento encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum agendamento encontrado na lixeira.', 'iande')
    ];

    $appointment_args = [
        'labels'             => $appointment_labels,
        'description'        => __('Agendamentos enviados pelos usuários.', 'iande'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'appointment'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => ['title', 'author', /* 'custom-fields' */]
    ];

    register_post_type('appointment', $appointment_args);


    /* Registra os metadados do post type appointment */

    $metadata_definition = get_appointment_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('appointment', $key, ['type' => $definition->type]);
    }
}


/**
 * Retorna a definição dos metadados do post type `appointment`
 *
 * @filter iande.appointment_metadata_definition
 * 
 * @return array
 */
function get_appointment_metadata_definition()
{
    $default_purpose_options = __("Objetivo 1\nObjetivo 2", 'iande');

    // @todo colocar em página de configuração
    $purpose_options = get_option('iande_appointment_purposes', $default_purpose_options);

    $metadata_definition = [
        'purpose' => (object) [
            'type' => 'string',
            'required' => __("O objetivo é obrigatório", 'iande'),
            'validation' => function ($value) use ($purpose_options) {
                if (in_array($value, $purpose_options)) {
                    return true;
                } else {
                    return __('Objetivo inválido', 'iande');
                }
            }
        ],
        'name' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            }
        ],
        'date' => (object) [
            'type' => 'string',
            'required' => __("A data é obrigatória", 'iande'),
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __("Formato de data inválido", 'iande');
                }
            }
        ]
    ];

    $metadata_definition = \apply_filters('iande.appointment_metadata_definition', $metadata_definition);

    return $metadata_definition;
}