<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_appointment');
add_action('init', 'Iande\\register_metabox_appointment');

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
 * Registra os metaboxes do agendamento
 *
 * @filter iande.appointment_metabox_fields
 *
 * @return void
 */
function register_metabox_appointment()
{

    /* Registra os metaboxes do post type appointment */

    $metadata_definition = get_appointment_metadata_definition();

    $fields = [];
    $appointment_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $appointment_metabox = new \Iande_Metabox(
                'appointment', // Slug/ID do Metabox (obrigatório)
                'Informações da Instituição', // Nome do Metabox  (obrigatório)
                'appointment', // Slug do Post Type, sendo possível enviar apenas um valor ou um array com vários (opcional)
                'normal', // Contexto (opções: normal, advanced, ou side) (opcional)
                'high' // Prioridade (opções: high, core, default ou low) (opcional)
            );

            $label       = '';
            $type        = '';
            $default     = '';
            $description = '';
            $options     = [];
            $attributes  = [];
            $size        = '';

            if (isset($definition->metabox->label))
                $label = $definition->metabox->label;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->default))
                $default = $definition->metabox->default;

            if (isset($definition->metabox->description))
                $description = $definition->metabox->description;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->size))
                $size = $definition->metabox->size;

            $fields[] = [
                'id'          => $key,
                'label'       => $label,
                'type'        => $type,
                'default'     => $default,
                'description' => $description,
                'options'     => $options,
                'attributes'  => $attributes,
                'size'        => $size
            ];
        }
    }

    $fields = \apply_filters('iande.appointment_metabox_fields', $fields);

    if (is_object($appointment_metabox))
        $appointment_metabox->set_fields($fields);

    return $appointment_metabox;
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
    $nature_options = ['institutional', 'other'];
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
            },
            'metabox' => (object) [
                'label'   => 'Perfil',
                'type'    => 'select',
                'options' => $purpose_options,
                'size'    => '50' // 75%, 50%, 33%, 25%, default 100%
            ]
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
            },
            'metabox' => (object) [
                'label' => __('Nome', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'label' => __('Data', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'label' => __('Hora', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
        ],
        'responsible_first_name' => (object) [
            'type' => 'string',
            'required' => __('O nome do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'label' => __('Nome do responsável', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
        ],
        'responsible_last_name' => (object) [
            'type' => 'string',
            'required' => __('O sobrenome do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'label' => __('Sobrenome do responsável', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'label' => __('E-mail do responsável', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'label' => __('Telefone do responsável', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'label'   => __('Relação do responsável com a instituição', 'iande'),
                'type'    => 'select',
                'options' => $role_options,
                'size'    => '50'
            ]
        ],
        'group_nature' => (object) [
            'type' => 'string',
            'required' => __('A natureza do grupo é obrigatória', 'iande'),
            'validation' => function ($value) use ($nature_options) {
                if (in_array($nature_options, $value)) {
                    return true;
                } else {
                    return __('Natureza do grupo inválida', 'iande');
                }
            },
            'metabox' => (object) [
                'label'   => __('Natureza do grupo', 'iande'),
                'type'    => 'select',
                'options' => $nature_options,
                'size'    => '50'
            ]
        ],
        'institution' => (object) [
            'type' => 'string',
            'required' => __('A instituição é obrigatória', 'iande'),
            'validation' => function ($value) {
                // TODO
                return true;
            }
        ]
    ];

    $metadata_definition = \apply_filters('iande.appointment_metadata_definition', $metadata_definition);

    return $metadata_definition;
}
