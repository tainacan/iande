<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_exhibition');
add_action('cmb2_admin_init', 'Iande\\register_metabox_exhibition');

/**
 * Registra o Post Type Exhibition
 */
function register_post_type_exhibition()
{
    $exhibition_labels = [
        'name'               => _x('Exposições', 'post type general name', 'iande'),
        'singular_name'      => _x('Exposição', 'post type singular name', 'iande'),
        'menu_name'          => _x('Exposições', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Exposição', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar nova', 'exposição', 'iande'),
        'add_new_item'       => __('Adicionar Nova Exposição', 'iande'),
        'new_item'           => __('Novo Exposição', 'iande'),
        'edit_item'          => __('Editar Exposição', 'iande'),
        'view_item'          => __('Ver Exposição', 'iande'),
        'all_items'          => __('Todos os Exposições', 'iande'),
        'search_items'       => __('Buscar Exposições', 'iande'),
        'parent_item_colon'  => __('Exposições Pais:', 'iande'),
        'not_found'          => __('Nenhuma Exposição encontrada.', 'iande'),
        'not_found_in_trash' => __('Nenhuma Exposição encontrada na lixeira.', 'iande')
    ];

    $exhibition_args = [
        'labels'             => $exhibition_labels,
        'description'        => __('Exposições adicionadas pelo museu.', 'iande'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'exhibition'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-format-image',
        'supports'           => ['title', /* 'author', 'custom-fields' */]
    ];

    register_post_type('exhibition', $exhibition_args);


    /* Registra os metadados do post type exhibition */

    $metadata_definition = get_exhibition_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('exhibition', $key, ['type' => $definition->type]);
    }
}

/**
 * Registra os metaboxes do agendamento com CMB2
 * 
 * @filter iande.exhibition_metabox_fields
 * 
 * @return void
 */
function register_metabox_exhibition() {

    /* Registra os metaboxes do post type exhibition */

    $metadata_definition = get_exhibition_metadata_definition();

    $fields = [];
    $exhibition_metabox = '';

    /* Cria o metabox */
    $exhibition_metabox = \new_cmb2_box([
        'id'            => 'exhibition',
        'title'         => __('Informações da Exposição', 'iande'),
        'object_types'  => ['exhibition'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true
    ]);

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            /**
             * Fields parameters
             * 
             * @link https://cmb2.io/docs/field-parameters
             */
            $name        = '';
            $desc        = '';
            $default     = '';
            $type        = '';
            $options     = [];
            $attributes  = [];
            $repeatable  = false;
            $date_format = 'Y-m-d';

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

            if (isset($definition->metabox->date_format))
                $date_format = $definition->metabox->date_format;

            $fields[] = [
                'name'        => $name,
                'desc'        => $desc,
                'id'          => $key,
                'default'     => $default,
                'type'        => $type,
                'options'     => $options,
                'attributes'  => $attributes,
                'repeatable'  => $repeatable,
                'date_format' => $date_format
            ];

        }
        
    }

    $fields = \apply_filters('iande.exhibition_metabox_fields', $fields);

    if (is_object($exhibition_metabox)) {
        
        foreach ($fields as $field) {

            if ($field['type'] == 'group') {

                $name          = '';
                $group_title   = '';
                $add_button    = '';
                $remove_button = '';
                $sortable      = true;
                $closed        = true;

                if (isset($field['name']))
                    $name = $field['name'];

                if (isset($field['options']['group_title']))
                    $group_title = $field['options']['group_title'];

                if (isset($field['options']['add_button']))
                    $add_button = $field['options']['add_button'];
                    
                if (isset($field['options']['remove_button']))
                    $remove_button = $field['options']['remove_button'];

                if (isset($field['options']['sortable']))
                    $sortable = $field['options']['sortable'];

                if (isset($field['options']['closed']))
                    $closed = $field['options']['closed'];

                $group_field = $exhibition_metabox->add_field([
                    'id'            => $field['id'],
                    'type'          => 'group',
                    'name'          => '<b>' . $name . '</b>',
                    'options'       => [
                        'group_title'   => $group_title,
                        'add_button'    => $add_button,
                        'remove_button' => $remove_button,
                        'sortable'      => $sortable,
                        'closed'        => $closed
                    ]
                ]);

                if (isset($definition->metabox->group_fields)) {

                    foreach ($definition->metabox->group_fields as $key => $each_field ) {
                        
                        $id         = '';
                        $name       = '';
                        $type       = '';
                        $desc       = '';
                        $repeatable = false;

                        if (isset($each_field['id']))
                            $id = $each_field['id'];

                        if (isset($each_field['name']))
                            $name = $each_field['name'];

                        if (isset($each_field['type']))
                            $type = $each_field['type'];

                        if (isset($each_field['desc']))
                            $desc = $each_field['desc'];

                        if (isset($each_field['repeatable']))
                            $repeatable = $each_field['repeatable'];

                        $exhibition_metabox->add_group_field($group_field, array(
                            'id'          => $id,
                            'name'        => $name,
                            'type'        => $type,
                            'description' => $desc,
                            'repeatable'  => $repeatable
                        ));

                    }

                }

            } else {   
                $exhibition_metabox->add_field($field);
            }
        }
    }

    return $exhibition_metabox;

}

/**
 * Retorna a definição dos metadados do post type `exhibition`
 *
 * @filter iande.exhibition_metadata_definition
 *
 * @return array
 */
function get_exhibition_metadata_definition() {

    $metadata_definition = [
        /**
         * @link https://cmb2.io/docs/field-types#-types-text_date
         */
        'date_from' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __("Formato de data inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name'        => __('Data (De)', 'iande'),
                'desc'        => __('Adicione a data de início das visitações a essa exposição', 'iande'),
                'type'        => 'text_date',
                'date_format' => 'Y-m-d',
            ]
        ],
        'date_to' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __("Formato de data inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name'        => __('Data (Até)', 'iande'),
                'desc'        => __('Adicione a data de término das visitações a essa exposição', 'iande'),
                'type'        => 'text_date',
                'date_format' => 'Y-m-d',
            ]
        ],
        'duration' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                // @todo
                return true;
            },
            'metabox' => (object) [
                'name' => __('Duração da visita', 'iande'),
                'type' => 'text_small',
            ]
        ],
        'group_size' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                // @todo
                return true;
            },
            'metabox' => (object) [
                'name' => __('Tamanho (máximo) dos grupos', 'iande'),
                'type' => 'text_small',
            ]
        ],
        'group_slot' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                // @todo
                return true;
            },
            'metabox' => (object) [
                'name' => __('Quantidade (máxima) de grupos por slot', 'iande'),
                'type' => 'text_small',
            ]
        ],
        'grid' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                // @todo
                return true;
            },
            'metabox' => (object) [
                'name' => __('Grid', 'iande'),
                'type' => 'text_small',
            ]
        ],
        'monday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Segunda-feira', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'tuesday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Terça-feira', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'wednesday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Quarta-feira', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'thursday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Quinta-feira', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'friday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Sexta-feira', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'saturday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Sábado', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ],
        'sunday' => (object) [
            'type'       => 'text',
            'required'   => true,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Domingo', 'iande'),
                'type'    => 'group',
                'options' => [
                    'group_title'   => __('Horário {#}', 'iande'),
                    'add_button'    => __('Adicionar novo horário', 'iande'),
                    'remove_button' => __('Remover horário', 'iande')
                ],
                'group_fields' => [
                    [
                        'id'          => 'from',
                        'name'        => __('De', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ],
                    [
                        'id'          => 'to',
                        'name'        => __('Até', 'iande'),
                        'type'        => 'text_time',
                        'time_format' => 'H:i',
                        'attributes'  => [
                            'data-timepicker' => json_encode([
                                'timeOnlyTitle' => __('Escolha o horário', 'iande'),
                                'timeFormat'    => 'HH:mm',
                                'stepMinute'    => 10,
                            ]),
                        ],
                    ]
                ]
            ]
        ]

    ];

    $metadata_definition = \apply_filters('iande.institution_metadata_definition', $metadata_definition);

    return $metadata_definition;

}