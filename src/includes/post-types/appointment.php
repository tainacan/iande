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
    $default_purpose_options = [
        'Ilustrar os conteúdos que estou trabalhando com esse grupo',
        'Complementar o processo educacional realizado pela instituição de origem do grupo',
        'Possibilitar ao grupo o acesso/conhecimento à exposições e museus',
        'Promover o aprendizado sobre os temas da exposição/museu',
        'Iniciar a exploração/descoberta de um novo tema',
        'Desenvolver a cultura geral do grupo',
        'Promover uma atividade de lazer'
    ];

    $default_role_options = [
        'professor',
        'orientador',
        'coordenador',
        'diretor',
        'guia de turismo',
        'outros'
    ];

    // @todo colocar em página de configuração
    $purpose_options = get_option('iande_appointment_purposes', $default_purpose_options);
    $role_options = get_option('iande_appointment_responsible_roles', $default_role_options);

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
        ],
        'hour' => (object) [
            'type' => 'string',
            'required' => __('O horário é obrigatório', 'iande'),
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat('H:i', $value);
                if ($d && $d->format('H:i') === $value) {
                    return true;
                } else {
                    return __("Formato de horário inválido", 'iande');
                }
            }
        ],
        'responsible_first_name' => (object) [
            'type' => 'string',
            'required' => __('O nome do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                return true;
            }
        ],
        'responsible_last_name' => (object) [
            'type' => 'string',
            'required' => __('O sobrenome do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                return true;
            }
        ],
        'responsible_email' => (object) [
            'type' => 'string',
            'required' => __('O e-mail do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                if (empty(filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    return __('Formato de e-mail do responsável inválido', 'iande');
                } else {
                    return true;
                }
            }
        ],
        'responsible_phone' => (object) [
            'type' => 'string',
            'required' => __('O telefone do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                if (empty(preg_match('/^\d{10,11}$/', $value))) {
                    return __('Formato de telefone do responsável inválido', 'iande');
                } else {
                    return true;
                }
            }
        ],
        'responsible_role' => (object) [
            'type' => 'string',
            'required' => __('A relação com a instituição é obrigatória', 'iande'),
            'validation' => function ($value) use ($role_options) {
                if (in_array($value, $role_options)) {
                    return true;
                } else {
                    return __('Relação com instituição inválida', 'iande');
                }
            }
        ]
    ];

    $metadata_definition = \apply_filters('iande.appointment_metadata_definition', $metadata_definition);

    return $metadata_definition;
}
