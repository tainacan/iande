<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_checkin');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_report');
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

    $users = \get_users();

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
        ],
        'educator_id' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value) use ($users) {
                if (is_numeric($value) && in_array($value, array_column($users, 'ID'))) {
                    return true;
                } else {
                    return __('O valor informado não corresponde a um usuário válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Educador', 'iande'),
                'type'    => 'select',
                'options' => map_users_to_options($users, true)
            ]
        ]

    ];

    $metadata_definition = \apply_filters('iande.group_metadata_definition', $metadata_definition);

    return $metadata_definition;
}

/**
 * Registra os metaboxes do checkin do grupo com CMB2
 *
 * @return void
 */
function register_metabox_group_checkin()
{

    $metadata_definition = get_group_checkin_metadata_definition();

    $metabox_definition = \new_cmb2_box([
        'id'           => 'group_checkin',
        'title'        => __('Informações do Checkin', 'iande'),
        'object_types' => ['group'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true,
        'show_on_cb'   => \current_user_can('manage_iande_options')
    ]);

    $fields = get_group_fields_parameters($metadata_definition, $metabox_definition);

    return $fields;

}

/**
 * Retorna a definição dos metadados do post type `group` relativos ao checkin
 *
 * @filter iande.group_checkin_metadata_definition
 *
 * @return array
 */
function get_group_checkin_metadata_definition()
{

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
        'has_checkin' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('O checkin foi realizado?', 'iande'),
                'type' => 'checkbox'
            ]
        ],
        'checkin_showed' => (object) [
            'type'       => 'string',
            'required'   => __('É necessário saber se o grupo apareceu', 'iande'),
            'validation' => function ($value) {
                if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('O grupo apareceu para a visita?', 'iande'),
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
        'checkin_num_people' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'yes' && empty($value)) {
                    return __('O número de pessoas é obrigatório', 'iande');
                }
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
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'yes' && empty($value)) {
                    return __('O número de responsáveis é obrigatório', 'iande');
                }
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
        'checkin_disabilities' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'yes' && empty($value)) {
                    return __('O número de pessoas com necessidade especial é obrigatório', 'iande');
                }
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
        'checkin_languages' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'yes' && empty($value)) {
                    return __('O número de pessoas falando outros idiomas é obrigatório', 'iande');
                }
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
        'checkin_age_range' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value, $params) {
                if ($params['group_showed'] == 'yes' && empty($value)) {
                    return __('A quantidade por faixa etária é obrigatória', 'iande');
                }
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
        'checkin_noshow_type' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'no' && empty($value)) {
                    return __('O motivo da não realização é obrigatório', 'iande');
                }
                if ($value == 'internal_problems' || $value == 'institution_problems') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('A visita não foi realizada devido a', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($checkin_noshow_type)
            ]
        ],
        'checkin_noshow_reason' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['group_showed'] == 'no' && empty($value)) {
                    return __('O motivo da não realização é obrigatório', 'iande');
                } else {
                    return true;
                }
            },
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
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_checkin_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Registra os metaboxes do report do grupo com CMB2
 *
 * @return void
 */
function register_metabox_group_report()
{

    $metadata_definition = get_group_report_metadata_definition();

    $metabox_definition = \new_cmb2_box([
        'id'           => 'group_report',
        'title'        => __('Informações da Avaliação do Educador', 'iande'),
        'object_types' => ['group'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true,
        'show_on_cb'   => \current_user_can('manage_iande_options')
    ]);

    $fields = get_group_fields_parameters($metadata_definition, $metabox_definition);

    return $fields;

}

/**
 * Retorna a definição dos metadados do post type `group` relativos ao report
 *
 * @filter iande.group_report_metadata_definition
 *
 * @return array
 */
function get_group_report_metadata_definition()
{

    /**
     * Desabilita o campo para usuários sem premissão de edição `manage_iande_options`
     */
    $disabled = (\current_user_can('manage_iande_options')) ? false : true;

    $type_options = [
        __('Mais expositiva', 'iande'),
        __('Mais dialogada', 'iande'),
        __('Mais direcionada', 'iande'),
        __('Mais livre', 'iande'),
        __('Mais teatral', 'iande'),
        __('Mais interrogativa', 'iande')
    ];

    $interest_options = [
        __('Elevado', 'iande'),
        __('Mediano', 'iande'),
        __('Fraco', 'iande')
    ];

    $mood_options = [
        __('Interesse', 'iande'),
        __('Apatia', 'iande'),
        __('Indisciplina', 'iande'),
        __('Tranquilidade', 'iande'),
        __('Participação', 'iande'),
        __('Outros', 'iande')
    ];

    $interactive_options = [
        __('Muito', 'iande'),
        __('Razoavelmente', 'iande'),
        __('Pouco', 'iande'),
        __('Nada', 'iande')
    ];

    $interaction_options = [
        __('Observação', 'iande'),
        __('Leitura', 'iande'),
        __('Interação com outros membros do grupo', 'iande'),
        __('Interação com o educador/proposta educativa', 'iande'),
        __('Interação com os aparatos expositivos/expografia', 'iande'),
        __('Interação com o material educativo', 'iande')
    ];

    $difficulty_options = [
        __('Atraso do grupo', 'iande'),
        __('Comportamento inadequado do grupo', 'iande'),
        __('Grupo muito grande', 'iande'),
        __('Omissão do responsável', 'iande'),
        __('Problemas relacionados à expografia', 'iande'),
        __('Museu muito cheio', 'iande'),
        __('Nenhum problema', 'iande'),
        __('Outros', 'iande')
    ];

    $metadata_definition = [
        'has_report' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('A avaliação do educador foi realizada?', 'iande'),
                'type' => 'checkbox'
            ]
        ],
        'report_type' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'              => __('Qual foi o grau de interesse da maior parte do grupo durante a visita?', 'iande'),
                'type'              => 'multicheck',
                'options'           => map_array_to_options($type_options),
                'select_all_button' => false,
                'attributes'        => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_interest' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Que tipo de visita você realizou? Marque até duas alternativas', 'iande'),
                'type'       => 'select',
                'options'    => map_array_to_options($interest_options),
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_mood' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'              => __('Como você classificaria a postura da maior parte do grupo durante a visita? Marque até duas alternativas', 'iande'),
                'type'              => 'multicheck',
                'options'           => map_array_to_options($mood_options),
                'select_all_button' => false,
                'attributes'        => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_mood_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Como você classificaria a postura da maior parte do grupo durante a visita (outro)?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_interactive' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('A visita educativa suscitou interações entre o visitante e a exposição?', 'iande'),
                'type'       => 'select',
                'options'    => map_array_to_options($interactive_options),
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_interaction' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'              => __('Que tipo de visita você realizou? Marque até duas alternativas', 'iande'),
                'type'              => 'multicheck',
                'options'           => map_array_to_options($interaction_options),
                'select_all_button' => false,
                'attributes'        => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_difficulty' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'              => __('Assinale as principais dificuldades encontradas. Marque até duas alternativas', 'iande'),
                'type'              => 'multicheck',
                'options'           => map_array_to_options($difficulty_options),
                'select_all_button' => false,
                'attributes'        => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_difficulty_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Assinale as principais dificuldades encontradas. Qual?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'report_summary' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Resumo da visita', 'iande'),
                'type'       => 'textarea',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_report_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Registra os metaboxes do feedback do grupo com CMB2
 *
 * @return void
 */
function register_metabox_group_feedback()
{

    $metadata_definition = get_group_feedback_metadata_definition();

    $metabox_definition = \new_cmb2_box([
        'id'           => 'group_feedback',
        'title'        => __('Informações da Avaliação do Visitante', 'iande'),
        'object_types' => ['group'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true,
        'show_on_cb'   => function () {
            return \current_user_can( 'read_feedback' );
        }
    ]);

    $fields = get_group_fields_parameters($metadata_definition, $metabox_definition);

    return $fields;

}

/**
 * Retorna a definição dos metadados do post type `group` relativos ao feedback
 *
 * @filter iande.group_feedback_metadata_definition
 *
 * @return array
 */
function get_group_feedback_metadata_definition()
{

    /**
     * Desabilita edição de todos campos do feedback
     */
    $disabled = true;

    $quality_options = [
        4 => __('Muito satisfatória', 'iande'),
        3 => __('Satisfatória', 'iande'),
        2 => __('Pouco satisfatória', 'iande'),
        1 => __('Insatisfatória', 'iande')
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
        'has_feedback' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('A avaliação do visitante foi realizada?', 'iande'),
                'type' => 'checkbox',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_visit' => (object) [
            'type'       => 'string',
            'required'   => __('O que você achou da visita educativa é obrigatório', 'iande'),
            'validation' => function ($value) {
                if (is_numeric($value) && intval($value) == $value && $value >= 1 && $value <= 4) {
                    return true;
                } else {
                    return __('O valor informado para o que você achou da visita educativa é inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('O que você achou da visita educativa?', 'iande'),
                'type'       => 'radio',
                'options'    => $quality_options,
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_educator' => (object) [
            'type'       => 'string',
            'required'   => __('A atuação do educador é obrigatória', 'iande'),
            'validation' => function ($value) {
                if (is_numeric($value) && intval($value) == $value && $value >= 1 && $value <= 4) {
                    return true;
                } else {
                    return __('A atuação do educador informada é inválida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('O que você achou da atuação do educador?', 'iande'),
                'type'       => 'radio',
                'options'    => $quality_options,
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_mood' => (object) [
            'type'       => 'string',
            'required'   => __('O que você acha sobre a atuação do educador sobre a reação do grupo, é obrigatório', 'iande'),
            'validation' => function ($value) use ($mood_options) {
                if (in_array($value, $mood_options) && !empty($value)) {
                    return true;
                } else {
                    return __('O que você acha sobre a atuação do educador é inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('Você acha que a atuação do educador suscitou que tipo de reação do grupo?', 'iande'),
                'type'       => 'radio',
                'options'    => map_array_to_options($mood_options),
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_mood_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Você acha que a atuação do educador suscitou que tipo de reação do grupo (outro)?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_liked' => (object) [
            'type'       => 'string',
            'required'   => __('O que você mais gostou na visita, é obrigatório', 'iande'),
            'validation' => function ($value) use ($liked_options) {
                if (in_array($value, $liked_options) && !empty($value)) {
                    return true;
                } else {
                    return __('O que informou para o que você mais gostou na visita é inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('O que você mais gostou na visita?', 'iande'),
                'type'       => 'radio',
                'options'    => map_array_to_options($liked_options),
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_liked_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('O que você mais gostou na visita (outro)?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_disliked' => (object) [
            'type'       => 'string',
            'required'   => __('O que você menos gostou na visita, é obrigatório', 'iande'),
            'validation' => function ($value) use ($disliked_options) {
                if (in_array($value, $disliked_options) && !empty($value)) {
                    return true;
                } else {
                    return __('O que informou para o que você menos gostou na visita é inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'       => __('O que você menos gostou na visita?', 'iande'),
                'type'       => 'radio',
                'options'    => map_array_to_options($disliked_options),
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_disliked_other' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('O que você mais gostou na visita (outro)?', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ],
        'feedback_comment' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'       => __('Deixe aqui seus comentários', 'iande'),
                'type'       => 'textarea',
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_feedback_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Retorna os parametros dos campos para os metadados do post type `group`
 *
 * @param array $metadata_definition com a definição dos metadados
 * @param object $metabox_definition objeto \new_cmb2_box com a definição do metabox
 *
 * @filter iande.' . $metabox_definition->meta_box['id'] . '_metabox_fields
 *
 * @link https://cmb2.io/docs/field-parameters
 *
 * @return array
 */
function get_group_fields_parameters(array $metadata_definition, object $metabox_definition)
{

    $fields = [];

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $name              = '';
            $desc              = '';
            $default           = '';
            $type              = '';
            $options           = [];
            $attributes        = [];
            $repeatable        = false;
            $select_all_button = false;

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

            if (isset($definition->metabox->select_all_button))
                $select_all_button = $definition->metabox->select_all_button;

            $fields[] = [
                'name'              => $name,
                'desc'              => $desc,
                'default'           => $default,
                'id'                => $key,
                'type'              => $type,
                'options'           => $options,
                'attributes'        => $attributes,
                'repeatable'        => $repeatable,
                'select_all_button' => $select_all_button
            ];

        }

    }

    $fields = \apply_filters('iande.' . $metabox_definition->meta_box['id'] . '_metabox_fields', $fields);

    if (is_object($metabox_definition)) {
        foreach ($fields as $field) {
            $metabox_definition->add_field($field);
        }
    }

    return $metabox_definition;

}

/**
 * Retorna todas definições dos metadados po post type `group`
 *
 * @filter iande.group_all_metadata_definition
 *
 * @return array
 */
function get_all_group_metadata_definition()
{

    $group_metadata_definition    = get_group_metadata_definition();
    $checkin_metadata_definition  = get_group_checkin_metadata_definition();
    $feedback_metadata_definition = get_group_feedback_metadata_definition();
    $report_metadata_definition   = get_group_report_metadata_definition();

    $metadata_definition = array_merge($group_metadata_definition, $checkin_metadata_definition, $feedback_metadata_definition, $report_metadata_definition);

    $metadata_definition = \apply_filters('iande.group_all_metadata_definition', $metadata_definition);

    return $metadata_definition;

}
