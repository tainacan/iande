<?php

namespace IandePlugin;

add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_checkin');

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
