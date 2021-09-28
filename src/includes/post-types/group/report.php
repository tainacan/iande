<?php

namespace IandePlugin;

add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_report');

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
        'show_on_cb'   => 'IandePlugin\\is_iande_staff'
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
                'options'           => map_array_to_options($type_options, false),
                'select_all_button' => false
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
                'options'    => map_array_to_options($interest_options)
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
                'options'           => map_array_to_options($mood_options, false),
                'select_all_button' => false
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
                'type'       => 'text'
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
                'options'    => map_array_to_options($interactive_options)
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
                'options'           => map_array_to_options($interaction_options, false),
                'select_all_button' => false
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
                'options'           => map_array_to_options($difficulty_options, false),
                'select_all_button' => false
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
                'type'       => 'text'
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
                'type'       => 'textarea'
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_report_metadata_definition', $metadata_definition);

    return $metadata_definition;

}
