<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_appointment');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_appointment');
add_action('before_delete_post', 'IandePlugin\\avoid_deleting_appointment');
add_action('wp_trash_post', 'IandePlugin\\avoid_deleting_appointment');

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
        'all_items'          => _x('Agendamentos', 'all items', 'iande'),
        'search_items'       => __('Buscar Agendamentos', 'iande'),
        'parent_item_colon'  => __('Agendamentos Pais:', 'iande'),
        'not_found'          => __('Nenhum agendamento encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum agendamento encontrado na lixeira.', 'iande')
    ];

    $appointment_args = [
        'labels'              => $appointment_labels,
        'description'         => __('Agendamentos enviados pelos usuários.', 'iande'),
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => 'iande-main-menu',
        'query_var'           => true,
        'rewrite'             => ['slug' => 'appointment'],
        'capability_type'     => 'appointment',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => ['title', 'author', /* 'custom-fields' */]
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
                'title'         => __('Informações do Agendamento', 'iande'),
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
            $multiple   = false;
            $limit      = \get_option('posts_per_page');
            $query_args = [
                'post_type'   => [ 'post', 'page' ],
                'post_status' => [ 'publish', 'pending' ]
            ];
            $show_on_cb = '';

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

            if(isset($definition->metabox->multiple_item))
                $multiple = $definition->metabox->multiple_item;

            if(isset($definition->metabox->limit))
                $limit = $definition->metabox->limit;

            if(isset($definition->metabox->query_args))
                $query_args = $definition->metabox->query_args;

            if (isset($definition->metabox->show_on_cb))
                $show_on_cb = $definition->metabox->show_on_cb;

            $fields[] = [
                'name'          => $name,
                'desc'          => $desc,
                'id'            => $key,
                'default'       => $default,
                'type'          => $type,
                'options'       => $options,
                'attributes'    => $attributes,
                'repeatable'    => $repeatable,
                'size'          => $size,
                'multiple-item' => $multiple,
                'limit'         => $limit,
                'query_args'    => $query_args,
                'show_on_cb'    => $show_on_cb
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
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'ID'
    ]);

    $institutions = \get_posts([
        'author'         =>  $user_id,
        'post_type'      => 'institution',
        'post_status'    => ['publish'],
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'title'
    ]);

    $metadata_definition = [
        'exhibition_id' => (object) [
            'type'          => 'integer',
            'required'      => __('A exposição é obrigatória', 'iande'),
            'validation'    => function ($value) use ($exhibitions) {
                if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $exhibitions)) {
                        return true;
                    } else {
                        return __('Exposição inválida', 'iande');
                    }
                } else {
                    return __('O valor informado para a exposição deve ser um número inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Exposição', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($exhibitions),
            ]
        ],
        'purpose' => (object) [
            'type'          => 'string',
            'required'      => __('O objetivo é obrigatório', 'iande'),
            'validation'    => function ($value) use ($purpose_options) {
                if (in_array($value, $purpose_options) && !empty($value)) {
                    return true;
                } else {
                    return __('Objetivo da visita inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => 'Objetivo da visita',
                'type'    => 'select',
                'options' => map_array_to_options($purpose_options)
            ]
        ],
        'purpose_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('Objetivo da visita (outro)', 'iande'),
                'type' => 'text'
            ]
        ],
        'name' => (object) [
            'type'       => 'string',
            'required'   => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Nome', 'iande'),
                'type' => 'text'
            ]
        ],
        'responsible_first_name' => (object) [
            'type'          => 'string',
            'required'      => __('O nome do responsável é obrigatório', 'iande'),
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Nome do responsável', 'iande'),
                'type' => 'text'
            ]
        ],
        'responsible_last_name' => (object) [
            'type'          => 'string',
            'required'      => __('O sobrenome do responsável é obrigatório', 'iande'),
            'validation'    => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Sobrenome do responsável', 'iande'),
                'type' => 'text'
            ]
        ],
        'responsible_email' => (object) [
            'type'          => 'string',
            'required'      => __('O e-mail do responsável é obrigatório', 'iande'),
            'validation'    => function ($value) {
                if (empty(filter_var($value, FILTER_VALIDATE_EMAIL))) {
                    return __('Formato de e-mail do responsável inválido', 'iande');
                } else {
                    return true;
                }
            },
            'metabox' => (object) [
                'name' => __('E-mail do responsável', 'iande'),
                'type' => 'text'
            ]
        ],
        'responsible_phone' => (object) [
            'type'          => 'string',
            'required'      => __('O telefone do responsável é obrigatório', 'iande'),
            'validation'    => function ($value) {
                if (empty(preg_match('/^\d{10,11}$/', $value))) {
                    return __('Formato de telefone do responsável inválido', 'iande');
                } else {
                    return true;
                }
            },
            'metabox' => (object) [
                'name' => __('Telefone do responsável', 'iande'),
                'type' => 'text'
            ]
        ],
        'responsible_role' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value, $params) use ($role_options) {
                $group_nature = $params['group_nature'];
                if (empty($group_nature) || $group_nature != 'institutional') {
                    return true;
                } elseif (empty($value)) {
                    return __('A relação com a instituição é obrigatória', 'iande');
                } elseif (!in_array($value, $role_options)) {
                    return __('Relação com instituição inválida', 'iande');
                } else {
                    return true;
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
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('Relação do responsável com a instituição (outra)', 'iande'),
                'type' => 'text'
            ]
        ],
        'group_nature' => (object) [
            'type'          => 'string',
            'required'      => __('A natureza do grupo é obrigatória', 'iande'),
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
                'options' => [
                    ''              => '',
                    'institutional' => __('Grupo institucional', 'iande'),
                    'other'         => _x('Outra', 'group', 'iande')
                ]
            ]
        ],
        'institution_id' => (object) [
            'type'          => 'integer',
            'required'      => false,
            'validation'    => function ($value, $params) use ($institutions) {
                $group_nature = $params['group_nature'];
                error_log('group_nature: ' . $group_nature);
                if (empty($group_nature) || $group_nature != 'institutional') {
                    return true;
                } else if (empty($value)) {
                    return __('A instituição é obrigatória', 'iande');
                } elseif (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $institutions)) {
                        return true;
                    } else {
                        return __('Instituição inválida', 'iande');
                    }
                } else {
                    return __('O valor informado para a instituição deve ser um número inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Instituição', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($institutions),
            ]
        ],
        'requested_exemption' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido para solicitação de isenção', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('Solicitou isenção dos ingressos?', 'iande'),
                'type'       => 'radio',
                'default'    => 'no',
                'show_on_cb' => 'use_exemption', // @see includes/cmb2/helpers.php
                'options'    => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'has_visited_previously' => (object) [
            'type'          => 'string',
            'required'      => __('Precisamos saber se você já visitou esse museu', 'iande'),
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido sobre visitação do museu', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Você já visitou o museu anteriormente?', 'iande'),
                'type'    => 'radio',
                'options' => array(
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ),
            ]
        ],
        'has_prepared_visit' => (object) [
            'type'          => 'string',
            'required'      => __('Precisamos saber se você preparou seu grupo para a visita', 'iande'),
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido sobre preparação do grupo', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Você preparou seu grupo para a visita?', 'iande'),
                'type' => 'radio',
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
                'type' => 'textarea_small'
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
                'type' => 'textarea_small'
            ]
        ],
        'confirmation_sent' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            }
        ],
        'groups' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'          => __('Grupos', 'iande'),
                'type'          => 'post_ajax_search',
                'multiple_item' => true,
                'query_args'    => [
                    'post_type' => ['group']
                ]

            ]
        ],
        'num_people' => (object) [
            'type'          => 'integer',
            'required'      => __('A quantidade prevista de pessoas é obrigatório', 'iande'),
            'validation'    => function ($value) {
                if (is_numeric($value)) {
                    return true;
                } else {
                    return __('O valor informado não é um número válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Quantidade prevista de pessoas', 'iande'),
                'type' => 'text'
            ]
        ],
        'reason_cancel' => (object) [
            'type'       => 'string',
            'validation' => function ($value) {
                $value = \esc_textarea($value);
                if (is_string($value)) {
                    return true;
                } else {
                    return __('Motivo de cancelamento inválido', 'iande');
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

/**
 * Impede a remoção acidental de Agendamentos, em vez de cancelamento
 *
 * @param integer $post_id ID do post a ser deletado
 */
function avoid_deleting_appointment() {
    global $post;

    if (!empty($post) && $post->post_type === 'appointment' && in_array($post->post_status, ['pending', 'publish'])) {
        wp_die(__('Agendamento não pode ser deletado. Tente cancelá-lo, em vez disso.', 'iande'));
    }
}
