<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_appointment');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_appointment');

require IANDE_PLUGIN_BASEPATH . 'includes/status-metabox.php';

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
 * Registra os metaboxes do agendamento com CMB2
 *
 * @filter iande.appointment_metabox_fields
 *
 * @return void
 */
function register_metabox_appointment() {

    /* Registra os metaboxes do post type appointment */

    $metadata_definition = get_appointment_metadata_definition();

    $fields = [];
    $appointment_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $appointment_metabox = \new_cmb2_box(array(
                'id'            => 'appointment',
                'title'         => __('Informações da Instituição', 'iande'),
                'object_types'  => array('appointment'),
                'context'       => 'normal',
                'priority'      => 'high',
                'show_names'    => true
            ));

            /**
             * Fields parameters
             *
             * @link https://cmb2.io/docs/field-parameters
             */
            $name       = '';
            $desc       = '';
            $default    = '';
            $type       = '';
            $options    = [];
            $attributes = [];
            $repeatable = false;
            $size       = '';

            if (isset($definition->metabox->name))
                $name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $desc = $definition->metabox->desc;

            if (isset($definition->metabox->default))
                $default = $definition->metabox->default;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $repeatable = $definition->metabox->repeatable;

            if (isset($definition->metabox->size))
                $size = $definition->metabox->size;

            $fields[] = [
                'name'       => $name,
                'desc'       => $desc,
                'id'         => $key,
                'default'    => $default,
                'type'       => $type,
                'options'    => $options,
                'attributes' => $attributes,
                'repeatable' => $repeatable,
                'size'       => $size
            ];

        }
    }

    $fields = \apply_filters('iande.appointment_metabox_fields', $fields);

    if (is_object($appointment_metabox)) {
        foreach ($fields as $field) {
            $appointment_metabox->add_field($field);
        }
    }

    return $appointment_metabox;
}

/**
 * Retorna a definição dos metadados do post type `appointment`
 *
 * @filter iande.appointment_metadata_definition
 *
 * @return array
 */
function get_appointment_metadata_definition() {

    // @todo colocar em página de configuração
    $nature_options = ['institutional', 'other'];

    $purpose_options = get_option('iande_appointments_settings', []);
    $purpose_options = isset($purpose_options['appointment_purpose']) ? $purpose_options['appointment_purpose'] : [];

    $purpose_options_new = [];
    foreach ($purpose_options as $key => $value) {
        $purpose_options_new[$value] = $value;
    }

    $purpose_options = $purpose_options_new;

    $role_options = get_option('iande_institution', []);
    $role_options = isset($role_options['institution_responsible_role']) ? $role_options['institution_responsible_role'] : [];

    $role_options_new = [];
    foreach ($role_options as $key => $value) {
        $role_options_new[$value] = $value;
    }

    $role_options = $role_options_new;

    $user_id = \get_the_author_meta('ID');


    $exhibitions = \get_posts([
        'post_type'      => 'exhibition',
        'post_status'    => ['publish'],
        'posts_per_page' => 9999,
        'order'          => 'ASC',
        'orderby'        => 'ID'
    ]);

    $institutions = \get_posts([
        'author'         =>  $user_id,
        'post_type'      => 'institution',
        'post_status'    => ['publish'],
        'posts_per_page' => 9999,
        'order'          => 'ASC',
        'orderby'        => 'title'
    ]);

    $metadata_definition = [
        'step' => (object) [
            'type'       => 'string',
            'validation' => function ($value) {
                // TODO
                return true;
            }
        ],
        'exhibition_id' => (object) [
            'type'          => 'string',
            'required'      => __('A exposição é obrigatória', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($exhibitions) {
                if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $exhibitions)) {
                        return true;
                    } else {
                        return __('Exposição inválida', 'iande');
                    }
                } else {
                    return __('O valor informado para a exposição deve ser um inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Exibição', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($exhibitions),
            ]
        ],
        'purpose' => (object) [
            'type'          => 'string',
            'required'      => __("O objetivo é obrigatório", 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($purpose_options) {
                if (in_array($value, $purpose_options) && !empty($value)) {
                    return true;
                } else {
                    return __('Objetivo da visita inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'   => 'Objetivo da visita',
                'type'    => 'select',
                'options' => map_array_to_options($purpose_options),
                'size'    => '50' // 75%, 50%, 33%, 25%, default 100%
            ]
        ],
        'purpose_other' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('Objetivo da visita (outro)', 'iande'),
                'type' => 'text',
                'size' => 50
            ]
        ],
        'name' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Nome', 'iande'),
                'type' => 'text',
                'size' => '50'
            ]
        ],
        'date' => (object) [
            'type'          => 'string',
            'required'      => __("A data é obrigatória", 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __("Formato de data inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Data', 'iande'),
                'type' => 'iande_date',
                'size' => '50'
            ]
        ],
        'hour' => (object) [
            'type'          => 'string',
            'required'      => __('O horário é obrigatório', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) {
                $d = \DateTime::createFromFormat('H:i', $value);
                if ($d && $d->format('H:i') === $value) {
                    return true;
                } else {
                    return __("Formato do horário inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Hora', 'iande'),
                'type' => 'iande_time',
                'size' => '50'
            ]
        ],
        'responsible_first_name' => (object) [
            'type'          => 'string',
            'required'      => __('O nome do responsável é obrigatório', 'iande'),
            'required_step' => 1,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Nome do responsável', 'iande'),
                'type' => 'text',
                'size' => '50'
            ]
        ],
        'responsible_last_name' => (object) [
            'type'          => 'string',
            'required'      => __('O sobrenome do responsável é obrigatório', 'iande'),
            'required_step' => 1,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Sobrenome do responsável', 'iande'),
                'type' => 'text',
                'size' => '50'
            ]
        ],
        'responsible_email' => (object) [
            'type'          => 'string',
            'required'      => __('O e-mail do responsável é obrigatório', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) {
                if (empty(filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    return __('Formato de e-mail do responsável inválido', 'iande');
                } else {
                    return true;
                }
            },
            'metabox' => (object) [
                'name' => __('E-mail do responsável', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
        ],
        'responsible_phone' => (object) [
            'type'          => 'string',
            'required'      => __('O telefone do responsável é obrigatório', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) {
                if (empty(preg_match('/^\d{10,11}$/', $value))) {
                    return __('Formato de telefone do responsável inválido', 'iande');
                } else {
                    return true;
                }
            },
            'metabox' => (object) [
                'name' => __('Telefone do responsável', 'iande'),
                'type' => 'text',
                'size' => '50'
            ]
        ],
        'responsible_role' => (object) [
            'type'          => 'string',
            'required'      => __('A relação com a instituição é obrigatória', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($role_options) {
                if (in_array($value, $role_options)) {
                    return true;
                } else {
                    return __('Relação com instituição inválida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Relação do responsável com a instituição', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($role_options),
                'size'    => '50'
            ]
        ],
        'responsible_role_other' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('Relação do responsável com a instituição (outra)', 'iande'),
                'type' => 'text',
                'size' => 50
            ]
        ],
        'group_nature' => (object) [
            'type'          => 'string',
            'required'      => __('A natureza do grupo é obrigatória', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($nature_options) {
                if (in_array($value, $nature_options)) {
                    return true;
                } else {
                    return __('Natureza do grupo inválida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Natureza do grupo', 'iande'),
                'type'    => 'select',
                'default' => 'institutional',
                'options' => [
                    'institutional' => __('Grupo institucional', 'iande'),
                    'other' => _x('Outra', 'group', 'iande')
                ],
                'size'    => '50'
            ]
        ],
        'institution_id' => (object) [
            'type'          => 'string',
            'required'      => __('A instituição é obrigatória', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($institutions) {
                if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $institutions)) {
                        return true;
                    } else {
                        return __('Instituição inválida', 'iande');
                    }
                } else {
                    return __('O valor informado para a instituição deve ser um inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Instituição', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($institutions),
            ]
        ],
        'has_visited_previously' => (object) [
            'type'          => 'string',
            'required'      => __('Precisamos saber se você já visitou esse museu', 'iande'),
            'required_step' => 2,
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Você já visitou o museu anteriormente?', 'iande'),
                'type' => 'radio',
                'size' => '50',
                'options'          => array(
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ),
            ]
        ],
        'has_prepared_visit' => (object) [
            'type'          => 'string',
            'required'      => __('Precisamos saber se você preparou seu grupo para a visita', 'iande'),
            'required_step' => 2,
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Você preparou seu grupo para a visita?', 'iande'),
                'type' => 'radio',
                'size' => '50',
                'options'          => array(
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ),
            ]
        ],
        'how_prepared_visit' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O texto informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('De que maneira você preparou o grupo?', 'iande'),
                'type' => 'textarea_small',
                'size' => '50'
            ]
        ],
        'additional_comment' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O texto informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Deseja comentar algo mais?', 'iande'),
                'type' => 'textarea_small',
                'size' => '50'
            ]
        ],
        'group_list' => (object) [
            'type'          => 'string',
            'required_step' => '2',
            'validation'    => function ($value) {
                // @todo validar json dos grupos enviados
                return true;
            },
            'metabox' => (object) [
                'name' => __("Grupos do agendamento", 'iande'),
                'type' => 'group_list',
            ]
        ],
        'reason_cancel' => (object) [
            'type'       => 'string',
            'validation' => function ($value) {
                $value = \esc_textarea($value);
                if (is_string($value)) {
                    return true;
                } else {
                    return __("Valor inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Motivo do cancelamento', 'iande'),
                'type' => 'textarea_small'
            ]
        ],
    ];

    $metadata_definition = \apply_filters('iande.appointment_metadata_definition', $metadata_definition);

    return $metadata_definition;

}
