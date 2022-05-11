<?php

namespace IandePlugin;

add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group_feedback');

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
            return \current_user_can('read_feedback');
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
     * Define a permissão de edição dos campos
     */
    $disabled   = true;
    $save_field = false;

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
        __('Do comportamento dos estudantes', 'iande'),
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
                'name'       => __('A avaliação do visitante foi realizada?', 'iande'),
                'type'       => 'checkbox',
                'save_field' => $save_field,
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
                'save_field' => $save_field,
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
                'save_field' => $save_field,
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
                'options'    => map_array_to_options($mood_options, false),
                'save_field' => $save_field,
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
                'save_field' => $save_field,
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
                'options'    => map_array_to_options($liked_options, false),
                'save_field' => $save_field,
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
                'save_field' => $save_field,
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
                'options'    => map_array_to_options($disliked_options, false),
                'save_field' => $save_field,
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
                'save_field' => $save_field,
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
                'save_field' => $save_field,
                'attributes' => [
                    'disabled' => $disabled
                ]
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.group_feedback_metadata_definition', $metadata_definition);

    return $metadata_definition;

}
