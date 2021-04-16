<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_checkin');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_feedback');

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
        'not_found'          => __('Nenhum Grupo encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum Grupo encontrado na lixeira.', 'iande')
    ];

    $group_args = [
        'labels'             => $group_labels,
        'description'        => __('Grupos para as visitas/agendamentos.', 'iande'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'iande-main-menu',
        'query_var'          => true,
        'rewrite'            => ['slug' => 'group'],
        'capability_type'    => 'group',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => ['title', 'author', /* 'custom-fields' */]
    ];

    register_post_type('group', $group_args);

    /**
     * Registra os metadados do post type `group`
     */
    $metadata_definition = get_group_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('group', $key, ['type' => $definition->type]);
    }
}

/**
 * Registra os metaboxes do grupo com CMB2
 *
 * @filter iande.group_metabox_fields
 *
 * @return void
 */
function register_metabox_group() {

    /* Registra os metaboxes do post type `group` */

    $metadata_definition = get_group_metadata_definition();

    $fields = [];
    $group_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $group_metabox = \new_cmb2_box(array(
                'id'            => 'group',
                'title'         => __('Informações do Grupo', 'iande'),
                'object_types'  => array('group'),
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
            $type       = '';
            $options    = [];
            $attributes = [];
            $repeatable = false;

            if (isset($definition->metabox->name))
                $name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $desc = $definition->metabox->desc;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $repeatable = $definition->metabox->repeatable;

            $fields[] = [
                'name'       => $name,
                'desc'       => $desc,
                'id'         => $key,
                'type'       => $type,
                'options'    => $options,
                'attributes' => $attributes,
                'repeatable' => $repeatable
            ];

        }

    }

    $fields = \apply_filters('iande.group_metabox_fields', $fields);

    if (is_object($group_metabox)) {
        foreach ($fields as $field) {
            $group_metabox->add_field($field);
        }
    }

    return $group_metabox;

}

/**
 * Retorna a definição dos metadados do post type `group`
 *
 * @filter iande.group_metadata_definition
 *
 * @return array
 */
function get_group_metadata_definition() {

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
        'post_status'    => ['draft', 'pending', 'publish'],
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

    $metadata_definition = [

        'appointment_id' => (object) [
            'type'          => 'integer',
            'required'      => __('O agendamento é obrigatório', 'iande'),
            'required_step' => 1,
            'validation'    => function ($value) use ($appointments) {
                if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $appointments)) {
                        return true;
                    } else {
                        return __('Agendamento inválido', 'iande');
                    }
                } else {
                    return __('O valor informado para o agendamento deve ser um inteiro', 'iande');
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
                'name'    => __('Exposição', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($exhibitions),
            ]
        ],
        'name' => (object) [
            'type'          => 'string',
            'required'      => __('O nome do grupo é obrigatório', 'iande'),
            'required_step' => 1,
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
        ]

    ];

    $metadata_definition = \apply_filters('iande.group_metadata_definition', $metadata_definition);

    return $metadata_definition;
}

/**
 * Registra os metaboxes do checkin do grupo com CMB2
 *
 * @filter iande.group_checkin_metabox_fields
 *
 * @return void
 */
function register_metabox_group_checkin() {

    $metadata_definition = get_group_checkin_metadata_definition();

    $fields = [];
    $group_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $group_metabox = \new_cmb2_box(array(
                'id'            => 'group_checkin',
                'title'         => __('Informações do Checkin', 'iande'),
                'object_types'  => array('group'),
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
            $type       = '';
            $options    = [];
            $attributes = [];
            $repeatable = false;

            if (isset($definition->metabox->name))
                $name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $desc = $definition->metabox->desc;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $repeatable = $definition->metabox->repeatable;

            $fields[] = [
                'name'       => $name,
                'desc'       => $desc,
                'id'         => $key,
                'type'       => $type,
                'options'    => $options,
                'attributes' => $attributes,
                'repeatable' => $repeatable
            ];

        }

    }

    $fields = \apply_filters('iande.group_checkin_metabox_fields', $fields);

    if (is_object($group_metabox)) {
        foreach ($fields as $field) {
            $group_metabox->add_field($field);
        }
    }

    return $group_metabox;

}

/**
 * Registra os metaboxes do feedback do grupo com CMB2
 *
 * @filter iande.group_feedback_metabox_fields
 *
 * @return void
 */
function register_metabox_group_feedback() {

    $metadata_definition = get_group_feedback_metadata_definition();

    $fields = [];
    $group_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $group_metabox = \new_cmb2_box(array(
                'id'            => 'group_feedback',
                'title'         => __('Informações da Avaliação', 'iande'),
                'object_types'  => array('group'),
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
            $type       = '';
            $options    = [];
            $attributes = [];
            $repeatable = false;

            if (isset($definition->metabox->name))
                $name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $desc = $definition->metabox->desc;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $repeatable = $definition->metabox->repeatable;

            $fields[] = [
                'name'       => $name,
                'desc'       => $desc,
                'id'         => $key,
                'type'       => $type,
                'options'    => $options,
                'attributes' => $attributes,
                'repeatable' => $repeatable
            ];

        }

    }

    $fields = \apply_filters('iande.group_feedback_metabox_fields', $fields);

    if (is_object($group_metabox)) {
        foreach ($fields as $field) {
            $group_metabox->add_field($field);
        }
    }

    return $group_metabox;

}

/**
 * Retorna a definição dos metadados do post type `group` relativos ao checkin
 *
 * @filter iande.group_checkin_metadata_definition
 *
 * @return array
 */
function get_group_checkin_metadata_definition() {

    $checkin_noshow_type = [
        __('Problemas internos', 'iande'),
        __('Problemas da instituição visitante', 'iande')
    ];
    
    $checkin_noshow_reason = [
        __('Problemas de deslocamento até a exposição/museu (trânsito, endereço errado, atraso do ônibus, atraso de alunos responsáveis)', 'iande'),
        __('O grupo preferiu visitar a exposição sem a presença do educador', 'iande'),
        __('O grupo optou por realizar outra atividade na instituição', 'iande'),
        __('A visita foi cancelada no mesmo dia', 'iande'),
        __('Não sei', 'iande'),
        __('Outro', 'iande')
    ];

    $metadata_definition = [
        'checkin_age_range' => (object) [
            'type'          => 'string',
            'required'      => __('Campo obrigatório', 'iande'),
            'validation'    => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Confirmação de quantidades por faixa etária', 'iande'),
                'type'    => 'radio',
                'options'          => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_disabilities' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de pessoas com cada tipo de necessidade especial', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_hour' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Horário efetivo de início da visita', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_languages' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de pessoas falando outros idiomas diferentes de português', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_noshow_type' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'internal_problems' || $value == 'institution_problems') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('A visita não foi realizada porque', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($checkin_noshow_type)
            ]
        ],
        'checkin_noshow_reason' => (object) [
            'type'     => 'string',
            'required' => __('Campo obrigatório', 'iande'),
            'metabox'  => (object) [
                'name'    => __('Qual desafio impossibilitou a visita?', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($checkin_noshow_reason)
            ]
        ],
        'checkin_noshow_reason_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('A visita não foi realizada porque (outro)', 'iande'),
                'type'    => 'text'
            ]
        ],
        'checkin_num_people' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de integrantes do grupo', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_num_people_actual' => (object) [
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
                'name'       => __('Quantas pessoas compareceram efetivamente?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0'
                ]
            ]
        ],
        'checkin_num_responsible' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de responsáveis', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ],
        'checkin_num_responsible_actual' => (object) [
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
                'name'       => __('Quantos responsáveis compareceram efetivamente?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'checkin_showed' => (object) [
            'type'       => 'string',
            'required'   => __('Campo obrigatório', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('O grupo compareceu para a visita', 'iande'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Sim', 'iande'),
                    'no'  => __('Não', 'iande')
                ]
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_checkin_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Retorna a definição dos metadados do post type `group` relativos ao feedback
 *
 * @filter iande.group_feedback_metadata_definition
 *
 * @return array
 */
function get_group_feedback_metadata_definition() {

    $quality_options = [
        __('Muito satisfatória', 'iande'),
        __('Satisfatória', 'iande'),
        __('Pouco satisfatória', 'iande'),
        __('Insatisfatória', 'iande')
    ];

    $mood_options = [
        __('Interesse', 'iande'),
        __('Apatia', 'iande'),
        __('Indisciplina', 'iande'),
        __('Tranquilidade', 'iande'),
        __('Participação', 'iande'),
        __('Outros', 'iande')
    ];

    $liked_options = [
        __('Observar o acervo', 'iande'),
        __('Interagir com a exposição', 'iande'),
        __('Ler os textos da exposição', 'iande'),
        __('Da atuação do educador/visita educativa', 'iande'),
        __('Dos materiais educativos', 'iande'),
        __('Outros', 'iande')
    ];

    $disliked_options = [
        __('Do acervo exposto', 'iande'),
        __('Dos textos da exposição', 'iande'),
        __('Da atuação do educador/visita educativa', 'iande'),
        __('Do comportamento dos alunos', 'iande'),
        __('Dos materiais educativos', 'iande'),
        __('Outros', 'iande')
    ];

    $metadata_definition = [
        'feedback_visit' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você achou da visita educativa?', 'iande'),
                'type'    => 'radio',
                'options' => map_array_to_options($quality_options)
            ]
        ],
        'feedback_educator' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você achou da atuação do educador?', 'iande'),
                'type'    => 'radio',
                'options' => map_array_to_options($quality_options)
            ]
        ],
        'feedback_mood' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Você acha que a atuação do educador suscitou que tipo de reação do grupo?', 'iande'),
                'type'    => 'radio',
                'options' => map_array_to_options($mood_options)
            ]
        ],
        'feedback_mood_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Você acha que a atuação do educador suscitou que tipo de reação do grupo (outro)?', 'iande'),
                'type'    => 'text'
            ]
        ],
        'feedback_liked' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você mais gostou na visita?', 'iande'),
                'type'    => 'radio',
                'options' => map_array_to_options($liked_options)
            ]
        ],
        'feedback_liked_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você mais gostou na visita (outro)?', 'iande'),
                'type'    => 'text'
            ]
        ],
        'feedback_disliked' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você mais gostou na visita?', 'iande'),
                'type'    => 'radio',
                'options' => map_array_to_options($disliked_options)
            ]
        ],
        'feedback_disliked_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('O que você mais gostou na visita (outro)?', 'iande'),
                'type'    => 'text'
            ]
        ],
        'feedback_comment' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Deixe aqui seus comentários', 'iande'),
                'type'    => 'textarea'
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_feedback_metadata_definition', $metadata_definition);

    return $metadata_definition;

}


/**
 * Adiciona os metadados do post_type `group` relativos ao checkin e feedback
 * no filtro iande.group_metadata_definition
 */
\add_filter('iande.group_metadata_definition', 'IandePlugin\\merge_metadata_definition');
function merge_metadata_definition($metadata_definition) {

    $checkin_metadata_definition = get_group_checkin_metadata_definition();
    $feedback_metadata_definition = get_group_feedback_metadata_definition();

    $metadata_definition = array_merge($metadata_definition, $checkin_metadata_definition, $feedback_metadata_definition); 

    return $metadata_definition;

}