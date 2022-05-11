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
        'title'        => __('Informações do Check-in', 'iande'),
        'object_types' => ['group'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true,
        'show_on_cb'   => 'IandePlugin\\is_iande_staff'
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

    $binary_options = [
        'yes' => __('Sim', 'iande'),
        'no'  => __('Não', 'iande')
    ];

    $checkin_noshow_reason = [
        __('Problemas de deslocamento até a exposição/museu (trânsito, endereço errado, atraso do ônibus, atraso de responsáveis)', 'iande'),
        __('O grupo preferiu visitar a exposição sem a presença do educador', 'iande'),
        __('O grupo optou por realizar outra atividade na instituição', 'iande'),
        __('A visita foi cancelada no mesmo dia', 'iande'),
        __('Não sei', 'iande'),
        __('Outro', 'iande')
    ];

    // Opções das instituições
    $iande_institution = get_option('iande_institution', []);

    // Perfil da instituições
    if (array_key_exists('institution_profile', $iande_institution)) {
        $institution_profile = $iande_institution['institution_profile'];
    } else {
        $institution_profile = [];
    }

    // Perfil etário
    if (array_key_exists('institution_age_range', $iande_institution)) {
        $institution_age_range = $iande_institution['institution_age_range'];
    } else {
        $institution_age_range = [];
    }

    // Escolaridade
    if (array_key_exists('institution_scholarity', $iande_institution)) {
        $institution_scholarity = $iande_institution['institution_scholarity'];
    } else {
        $institution_scholarity = [];
    }

    $metadata_definition = [
        'has_checkin' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => __('O check-in foi realizado?', 'iande'),
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
                'options' => $binary_options
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
                'options' => $binary_options
            ]
        ],
        'checkin_num_people' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('O número de pessoas é obrigatório', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de integrantes do grupo', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
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
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('O número de responsáveis é obrigatório', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de responsáveis', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
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
                ]
            ]
        ],
        'checkin_disabilities' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('O número de pessoas com deficiência é obrigatório', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de pessoas com cada tipo de deficiência', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
            ]
        ],
        'checkin_disabilities_actual' => (object) [
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
        'checkin_languages' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('O número de pessoas falando outros idiomas é obrigatório', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Quantidade efetiva de pessoas falando outros idiomas diferentes de português', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
            ]
        ],
        'checkin_languages_actual' => (object) [
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
                'name'       => __('Confirmação dos idiomas', 'iande'),
                'type'       => 'languages',
                'repeatable' => true,
                'options'    => [
                    'add_row_text' => __('Adicionar Idioma', 'iande')
                ]
            ]
        ],
        'checkin_scholarity' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('A escolaridade é obrigatória', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Confirmação de escolaridade', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
            ]
        ],
        'checkin_scholarity_actual' => (object) [
            'type'       => 'string',
            'required'   => __('A escolaridade é obrigatória', 'iande'),
            'validation' => function ($value) use ($institution_scholarity) {
                if (is_string($value) || is_array($value)) {
                    if (in_array($value, $institution_scholarity)) {
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
                'options' => map_array_to_options($institution_scholarity)
            ]
        ],
        'checkin_age_range' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('A faixa etária é obrigatória', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Confirmação de faixa etária', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
            ]
        ],
        'checkin_age_range_actual' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) use ($institution_age_range) {
                if (is_string($value) || is_array($value)) {
                    if (in_array($value, $institution_age_range)) {
                        return true;
                    } else {
                        return __('Faixa etária inválida', 'iande');
                    }
                } else {
                    return __('O valor informado não é uma string válida', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Confirmação de faixa etária ', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($institution_age_range)
            ]
        ],
        'checkin_institutional' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value, $params) {
                if ($params['checkin_showed'] == 'no') {
                    return true;
                } else if (empty($value)) {
                    return __('O tipo de instituição é obrigatório', 'iande');
                } else if ($value == 'yes' || $value == 'no') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('O grupo é institucional?', 'iande'),
                'type'    => 'radio',
                'options' => $binary_options
            ]
        ],
        'checkin_institution' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Tipo / perfil da instituição', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($institution_profile)
            ]
        ],
        'checkin_institution_actual' => (object) [
            'type'          => 'string',
            'required'      => false,
            'validation'    => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Confirmação do tipo / perfil da instituição', 'iande'),
                'type'    => 'select',
                'options' => map_array_to_options($institution_profile)
            ]
        ],
        'checkin_noshow_type' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'yes') {
                    return true;
                } else if (empty($value)) {
                    return __('O motivo da não realização é obrigatório', 'iande');
                } else if ($value == 'internal' || $value == 'visitor') {
                    return true;
                } else {
                    return __('Valor inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('A visita não foi realizada devido a', 'iande'),
                'type'    => 'select',
                'options' => [
                    ''         => '',
                    'internal' =>__('Problemas internos', 'iande'),
                    'visitor'  => __('Problemas da instituição visitante', 'iande'),
                ]
            ]
        ],
        'checkin_noshow_reason' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value, $params) {
                if ($params['checkin_showed'] == 'yes') {
                    return true;
                } else if (empty($value)) {
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
                'name' => __('A visita não foi realizada porque (outro)', 'iande'),
                'type' => 'text'
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_checkin_metadata_definition', $metadata_definition);

    return $metadata_definition;

}
