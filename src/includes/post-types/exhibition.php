<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_exhibition');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_exhibition');

require IANDE_PLUGIN_BASEPATH . 'includes/itinerary-metabox.php';

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
        'new_item'           => __('Nova Exposição', 'iande'),
        'edit_item'          => __('Editar Exposição', 'iande'),
        'view_item'          => __('Ver Exposição', 'iande'),
        'all_items'          => _x('Exposições', 'all items', 'iande'),
        'search_items'       => __('Buscar Exposições', 'iande'),
        'parent_item_colon'  => __('Exposições Pais:', 'iande'),
        'not_found'          => __('Nenhuma exposição encontrada.', 'iande'),
        'not_found_in_trash' => __('Nenhuma exposição encontrada na lixeira.', 'iande')
    ];

    $exhibition_args = [
        'labels'              => $exhibition_labels,
        'description'         => __('Exposições adicionadas pelo museu.', 'iande'),
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => 'iande-main-menu',
        'query_var'           => true,
        'rewrite'             => ['slug' => 'exhibition'],
        'capability_type'     => 'exhibition',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-format-image',
        'supports'            => ['title', /* 'author', 'custom-fields' */]
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
            $mb_name         = '';
            $mb_desc         = '';
            $mb_default      = '';
            $mb_type         = '';
            $mb_options      = [];
            $mb_group_fields = [];
            $mb_attributes   = [];
            $mb_repeatable   = false;
            $mb_date_format  = 'Y-m-d';
            $mb_multiple   = false;
            $mb_limit      = \get_option('posts_per_page');
            $mb_query_args = [
                'post_type'   => ['post', 'page'],
                'post_status' => ['publish', 'pending']
            ];
            $mb_show_on_cb = '';

            if (isset($definition->metabox->name))
                $mb_name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $mb_desc = $definition->metabox->desc;

            if (isset($definition->metabox->default))
                $mb_default = $definition->metabox->default;

            if (isset($definition->metabox->type))
                $mb_type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $mb_options = $definition->metabox->options;

            if (isset($definition->metabox->group_fields))
                $mb_group_fields = $definition->metabox->group_fields;

            if (isset($definition->metabox->attributes))
                $mb_attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $mb_repeatable = $definition->metabox->repeatable;

            if (isset($definition->metabox->date_format))
                $mb_date_format = $definition->metabox->date_format;

            if (isset($definition->metabox->multiple_item))
                $mb_multiple = $definition->metabox->multiple_item;

            if (isset($definition->metabox->limit))
                $mb_limit = $definition->metabox->limit;

            if (isset($definition->metabox->query_args))
                $mb_query_args = $definition->metabox->query_args;

            if (isset($definition->metabox->show_on_cb))
                $mb_show_on_cb = $definition->metabox->show_on_cb;

            $fields[] = [
                'name'          => $mb_name,
                'desc'          => $mb_desc,
                'id'            => $key,
                'default'       => $mb_default,
                'type'          => $mb_type,
                'options'       => $mb_options,
                'group_fields'  => $mb_group_fields,
                'attributes'    => $mb_attributes,
                'repeatable'    => $mb_repeatable,
                'date_format'   => $mb_date_format,
                'multiple-item' => $mb_multiple,
                'limit'         => $mb_limit,
                'query_args'    => $mb_query_args,
                'show_on_cb'    => $mb_show_on_cb
            ];

        }

    }

    $fields = \apply_filters('iande.exhibition_metabox_fields', $fields);

    if (is_object($exhibition_metabox)) {

        foreach ($fields as $field) {

            if ($field['type'] == 'group') {

                $f_name          = '';
                $f_group_title   = '';
                $f_add_button    = '';
                $f_remove_button = '';
                $f_sortable      = true;
                $f_closed        = true;

                if (isset($field['name']))
                    $f_name = $field['name'];

                if (isset($field['options']['group_title']))
                    $f_group_title = $field['options']['group_title'];

                if (isset($field['options']['add_button']))
                    $f_add_button = $field['options']['add_button'];

                if (isset($field['options']['remove_button']))
                    $f_remove_button = $field['options']['remove_button'];

                if (isset($field['options']['sortable']))
                    $f_sortable = $field['options']['sortable'];

                if (isset($field['options']['closed']))
                    $f_closed = $field['options']['closed'];

                $group_field = $exhibition_metabox->add_field([
                    'id'            => $field['id'],
                    'type'          => 'group',
                    'name'          => '<b>' . $f_name . '</b>',
                    'options'       => [
                        'group_title'   => $f_group_title,
                        'add_button'    => $f_add_button,
                        'remove_button' => $f_remove_button,
                        'sortable'      => $f_sortable,
                        'closed'        => $f_closed
                    ]
                ]);

                if (isset($field['group_fields'])) {

                    foreach ($field['group_fields'] as $gf_key => $each_field ) {

                        $gf_id         = '';
                        $gf_name       = '';
                        $gf_type       = '';
                        $gf_desc       = '';
                        $gf_repeatable = false;

                        if (isset($each_field['id']))
                            $gf_id = $each_field['id'];

                        if (isset($each_field['name']))
                            $gf_name = $each_field['name'];

                        if (isset($each_field['type']))
                            $gf_type = $each_field['type'];

                        if (isset($each_field['desc']))
                            $gf_desc = $each_field['desc'];

                        if (isset($each_field['repeatable']))
                            $gf_repeatable = $each_field['repeatable'];

                        $exhibition_metabox->add_group_field($group_field, array(
                            'id'          => $gf_id,
                            'name'        => $gf_name,
                            'type'        => $gf_type,
                            'description' => $gf_desc,
                            'repeatable'  => $gf_repeatable
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
        'calendar_appointments' => (object) [
            'type' => 'string',
            'validation' => function ($value) {
                return true;
            },
            'metabox' => (object) [
                'name' => '',
                'type' => 'calendar_appointments'
            ]
        ],
        'description' => (object) [
            'type'       => 'string',
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O texto informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Descrição', 'iande'),
                'type' => 'textarea_small',
                'size' => '50'
            ]
        ],
        'date_from' => (object) [
            'type'       => 'string',
            'required'   => true,
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __('Formato de data inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'        => __('Data (De)', 'iande'),
                'desc'        => __('Adicione a data de início das visitações a essa exposição', 'iande'),
                'type'        => 'iande_date',
            ]
        ],
        'date_to' => (object) [
            'type'       => 'string',
            'required'   => true,
            'validation' => function ($value) {
                $d = \DateTime::createFromFormat("Y-m-d", $value);
                if ($d && $d->format("Y-m-d") === $value) {
                    return true;
                } else {
                    return __('Formato de data inválido', 'iande');
                }
            },
            'metabox' => (object) [
                'name'        => __('Data (Até)', 'iande'),
                'desc'        => __('Adicione a data de término das visitações a essa exposição', 'iande'),
                'type'        => 'iande_date',
            ]
        ],
        'duration' => (object) [
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
                'name' => __('Duração da visita', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'min_group_size' => (object) [
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
                'name' => __('Tamanho mínimo dos grupos', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'group_size' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value, $params) {
                if (!is_numeric($value)) {
                    return __('O valor informado não é um número válido', 'iande');
                } else if ($params['min_group_size'] && $value < $params['min_group_size']) {
                    return __('O tamanho deve ser igual ou maior do que o tamanho mínimo', 'iande');
                } else {
                    return true;
                }
            },
            'metabox' => (object) [
                'name' => __('Tamanho máximo dos grupos', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'max_num_responsible' => (object) [
            'type'       => 'integer',
            'required'   => false,
            'validation' => function ($value) {
                if (is_numeric($value)) {
                    if (intval($value) <= 1) {
                        return __('O valor informado é muito pequeno', 'iande');
                    } else {
                        return true;
                    }
                } else {
                    return __('O valor informado não é um número válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Número máximo de responsáveis', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '1',
                ],
            ]
        ],
        'group_slot' => (object) [
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
                'name' => __('Quantidade (máxima) de grupos por horário', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'grid' => (object) [
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
                'name'       => __('Intervalo entre os horários de atendimento', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],
        'days_advance' => (object) [
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
                'name' => __('Antecedência mínima (em dias)', 'iande'),
                'type'       => 'text',
                'attributes' => [
                    'type' => 'number',
                    'min'  => '0',
                ],
            ]
        ],

        'monday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Segunda-feira', 'iande'))

        ],
        'tuesday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Terça-feira', 'iande'))
        ],
        'wednesday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Quarta-feira', 'iande'))
        ],
        'thursday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Quinta-feira', 'iande'))
        ],
        'friday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Sexta-feira', 'iande'))
        ],
        'saturday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Sábado', 'iande'))
        ],
        'sunday' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => get_group_fields_metadata(__('Domingo', 'iande'))
        ],
        'exception' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'          => __('Horários especiais', 'iande'),
                'type'          => 'post_ajax_search',
                'show_on_cb'    => 'has_exception',
                'multiple_item' => true,
                'query_args'    => [
                    'post_type' => 'exception'
                ]
            ]
        ]

    ];

    $metadata_definition = \apply_filters('iande.exhibition_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Imprime os group_fields para os campos de horários
 *
 * @param string $name
 * @return void
 */
function get_group_fields_metadata($name)
{

    $object =  (object) [
        'name'    => $name,
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
                'type'        => 'iande_time',
            ],
            [
                'id'          => 'to',
                'name'        => __('Até', 'iande'),
                'type'        => 'iande_time',
            ]
        ]
    ];

    return $object;

}