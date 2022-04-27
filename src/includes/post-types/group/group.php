<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group');

/**
 * Registra o Post Type `group`
 */
function register_post_type_group()
{
    $group_labels = [
        'name'               => _x('Grupos', 'post type general name', 'iande'),
        'singular_name'      => _x('Grupo', 'post type singular name', 'iande'),
        'menu_name'          => _x('Grupos', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Grupo', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar novo', 'Grupo', 'iande'),
        'add_new_item'       => __('Adicionar Novo Grupo', 'iande'),
        'new_item'           => __('Novo Grupo', 'iande'),
        'edit_item'          => __('Editar Grupo', 'iande'),
        'view_item'          => __('Ver Grupo', 'iande'),
        'all_items'          => _x('Grupos', 'all items', 'iande'),
        'search_items'       => __('Buscar Grupos', 'iande'),
        'parent_item_colon'  => __('Grupos Pais:', 'iande'),
        'not_found'          => __('Nenhum grupo encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum grupo encontrado na lixeira.', 'iande')
    ];

    $group_args = [
        'labels'              => $group_labels,
        'description'         => __('Grupos para as visitas/agendamentos.', 'iande'),
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => 'iande-main-menu',
        'query_var'           => true,
        'rewrite'             => ['slug' => 'group'],
        'capability_type'     => 'group',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-groups',
        'supports'            => ['title', 'author', /* 'custom-fields' */]
    ];

    register_post_type('group', $group_args);

    /**
     * Registra os metadados do post type `group`
     */
    $metadata_definition = get_all_group_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('group', $key, ['type' => $definition->type]);
    }
}

/**
 * Registra os metaboxes do grupo com CMB2
 *
 * @return void
 */
function register_metabox_group()
{

    $metadata_definition = get_group_metadata_definition();

    $metabox_definition = \new_cmb2_box([
        'id'           => 'group',
        'title'        => __('Informações do Grupo', 'iande'),
        'object_types' => ['group'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true
    ]);

    $fields = get_group_fields_parameters($metadata_definition, $metabox_definition);

    return $fields;

}

/**
 * Retorna a definição dos metadados do post type `group`
 *
 * @filter iande.group_metadata_definition
 *
 * @return array
 */
function get_group_metadata_definition()
{

    // perfil etario
    $iande_institution = get_option('iande_institution', []);
    if (array_key_exists('institution_age_range', $iande_institution)) {
        $age_range = $iande_institution['institution_age_range'];
    } else {
        $age_range = [];
    }

    // escolaridade
    $iande_institution = get_option('iande_institution', []);
    if (array_key_exists('institution_scholarity', $iande_institution)) {
        $scholarity = $iande_institution['institution_scholarity'];
    } else {
        $scholarity = [];
    }

    $appointments = \get_posts([
        'post_type'      => 'appointment',
        'post_status'    => ['draft', 'canceled', 'pending', 'publish'],
        'posts_per_page' => -1,
        'order'          => 'DESC',
        'orderby'        => 'ID',
    ]);
    $exhibitions = \get_posts([
        'post_type'      => 'exhibition',
        'post_status'    => ['pending', 'publish'],
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'ID',
    ]);

    $staff_roles = [];
    foreach (\wp_roles()->roles as $role_slug => $role) {
        if (!empty($role['capabilities']['checkin'])) {
            $staff_roles[] = $role_slug;
        }
    }

    $staff_users = \get_users([
        'role__in' => $staff_roles,
    ]);

    $metadata_definition = [

        'appointment_id' => (object) [
            'type'          => 'integer',
            'required'      => __('O agendamento é obrigatório', 'iande'),
            'validation'    => function ($value) use ($appointments) {
                if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $appointments)) {
                        return true;
                    } else {
                        return __('Agendamento inválido', 'iande');
                    }
                } else {
                    return __('O valor informado para o agendamento deve ser um número inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Agendamento', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($appointments),
            ]
        ],
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
        'name' => (object) [
            'type'          => 'string',
            'required'      => __('O nome do grupo é obrigatório', 'iande'),
            'validation'    => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('Nome', 'iande'),
                'type' => 'text',
            ]
        ],
        'num_people' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value) {
                if (is_numeric($value)) {
                    return true;
                } else {
                    return __('O valor informado não é um número válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Quantidade prevista de pessoas', 'iande'),
                'type'  => 'text'
            ]
        ],
        'age_range' => (object) [
            'type'       => 'string',
            'required'   => __('A faixa etária é obrigatória', 'iande'),
            'validation' => function ($value) use ($age_range) {
                if (is_string($value) || is_array($value)) {
                    if (in_array($value, $age_range)) {
                        return true;
                    } else {
                        return __('Faixa etária inválida', 'iande');
                    }
                } else {
                    return __('O valor informado não é uma string válida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Perfil etário do grupo', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($age_range),
            ]
        ],
        'scholarity' => (object) [
            'type'       => 'string',
            'required'   => __('A escolaridade é obrigatória', 'iande'),
            'validation' => function ($value) use ($scholarity) {
                if (is_string($value) || is_array($value)) {
                    if (in_array($value, $scholarity)) {
                        return true;
                    } else {
                        return __('Escolaridade inválida', 'iande');
                    }
                } else {
                    return __('O valor informado não é uma string válida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Escolaridade', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($scholarity),
            ]
        ],
        'num_responsible' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value) {
                if (is_numeric($value)) {
                    return true;
                } else {
                    return __('O valor informado não é um número válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Quantidade prevista de responsáveis', 'iande'),
                'type'  => 'text'
            ]
        ],
        'date' => (object) [
            'type'       => 'string',
            'required'   => __('A data é obrigatória', 'iande'),
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __('Formato de data inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Data', 'iande'),
                'type' => 'iande_date'
            ]
        ],
        'hour' => (object) [
            'type'       => 'string',
            'required'   => __('O horário é obrigatório', 'iande'),
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat('H:i', $value);
                if ($d && $d->format('H:i') === $value) {
                    return true;
                } else {
                    return __('Formato do horário inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Hora', 'iande'),
                'type' => 'iande_time'
            ]
        ],
        'languages' => (object) [
            'type'       => 'object',
            'required'   => false,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
                } else {
                    return __('O valor informado não é válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('Idiomas', 'iande'),
                'type'       => 'languages',
                'repeatable' => true,
                'options'    => [
                    'add_row_text' => __('Adicionar Idioma', 'iande')
                ]
            ]
        ],
        'disabilities' => (object) [
            'type'       => 'object',
            'required'   => false,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
                } else {
                    return __('O valor informado não é válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('Deficiências', 'iande'),
                'type'       => 'disabilities',
                'repeatable' => true,
                'options'    => [
                    'add_row_text' => __('Adicionar Deficiência/Quantidade', 'iande')
                ]
            ]
        ],
        'educator_id' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value) use ($staff_users) {
                if (is_numeric($value) && in_array($value, array_column($staff_users, 'ID'))) {
                    return true;
                } else {
                    return __('O valor informado não corresponde a um usuário válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Educador', 'iande'),
                'type'    => 'select',
                'options' => map_users_to_options($staff_users),
            ]
        ],
        'confirmation_sent_after_visiting' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            }
        ]

    ];

    $metadata_definition = \apply_filters('iande.group_metadata_definition', $metadata_definition);

    return $metadata_definition;
}
