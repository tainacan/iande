<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_exception');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_exception');
add_action('edit_form_after_title', 'IandePlugin\\add_exception_explainer');

/**
 * Registra o Post Type Exception
 */
function register_post_type_exception()
{

    $exception_labels = [
        'name'               => _x('Horários especiais', 'post type general name', 'iande'),
        'singular_name'      => _x('Horário especial', 'post type singular name', 'iande'),
        'menu_name'          => _x('Horários especiais', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Horário especial', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar novo', 'horário especial', 'iande'),
        'add_new_item'       => __('Adicionar Novo Horário Especial', 'iande'),
        'new_item'           => __('Novo Horário Especial', 'iande'),
        'edit_item'          => __('Editar Horário Especial', 'iande'),
        'view_item'          => __('Ver Horário Especial', 'iande'),
        'all_items'          => _x('Horários Especiais', 'all items', 'iande'),
        'search_items'       => __('Buscar Horários Especiais', 'iande'),
        'parent_item_colon'  => __('Horários Pais:', 'iande'),
        'not_found'          => __('Nenhum horário especial encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum horário especial encontrado na lixeira.', 'iande')
    ];

    $exception_args = [
        'labels'              => $exception_labels,
        'description'         => __('Exceções para o horário de funcionamento das exposições.', 'iande'),
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => 'iande-main-menu',
        'query_var'           => true,
        'rewrite'             => ['slug' => 'exception'],
        'capability_type'     => 'exception',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-hidden',
        'supports'            => ['title', /* 'author', 'custom-fields' */]
    ];

    register_post_type('exception', $exception_args);

    /* Registra os metadados do post type exception */

    $metadata_definition = get_exception_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('exception', $key, ['type' => $definition->type]);
    }

}

/**
 * Registra os metaboxes do agendamento com CMB2
 *
 * @filter iande.exception_metabox_fields
 *
 * @return void
 */
function register_metabox_exception() {

    /* Registra os metaboxes do post type exception */

    $metadata_definition = get_exception_metadata_definition();

    $fields = [];
    $exception_metabox = '';

    /* Cria o metabox */
    $exception_metabox = \new_cmb2_box([
        'id'            => 'exception',
        'title'         => __('Informações do Horário Especial', 'iande'),
        'object_types'  => ['exception'],
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

            $fields[] = [
                'name'         => $mb_name,
                'desc'         => $mb_desc,
                'id'           => $key,
                'default'      => $mb_default,
                'type'         => $mb_type,
                'options'      => $mb_options,
                'group_fields' => $mb_group_fields,
                'attributes'   => $mb_attributes,
                'repeatable'   => $mb_repeatable,
                'date_format'  => $mb_date_format
            ];

        }

    }

    $fields = \apply_filters('iande.exception_metabox_fields', $fields);

    if (is_object($exception_metabox)) {

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

                $group_field = $exception_metabox->add_field([
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

                        $exception_metabox->add_group_field($group_field, array(
                            'id'          => $gf_id,
                            'name'        => $gf_name,
                            'type'        => $gf_type,
                            'description' => $gf_desc,
                            'repeatable'  => $gf_repeatable
                        ));

                    }

                }

            } else {
                $exception_metabox->add_field($field);
            }
        }
    }

    return $exception_metabox;

}

/**
 * Retorna a definição dos metadados do post type `exception`
 *
 * @filter iande.exception_metadata_definition
 *
 * @return array
 */
function get_exception_metadata_definition()
{

    $metadata_definition = [
        'date_from' => (object) [
            'type'       => 'string',
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
                    return __("Formato de data inválido", 'iande');
                }
            },
            'metabox' => (object) [
                'name'        => __('Data (Até)', 'iande'),
                'type'        => 'iande_date',
            ]
        ],
        'exception' => (object) [
            'type'       => 'text',
            'required'   => false,
            'validation' => function ($value) {
                //@todo
                return true;
            },
            'metabox' => (object) [
                'name'    => __('Horários Especiais', 'iande'),
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
            ]
        ],

    ];

    $metadata_definition = \apply_filters('iande.exception_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Adicionar texto de tutorial ao editor de horários especiais
 */
function add_exception_explainer () {
    global $post;

    if (!empty($post) && $post->post_type === 'exception') { ?>
        <div class="notice notice-info inline">
            <ul>
                <li><?= __('Horários vazios fecham o museu nas datas selecionadas.', 'iande') ?></li>
                <li><?= __('Horários preenchidos substituem os horários padrões nas datas selecionadas.', 'iande') ?></li>
                <li><?= __('Os horários especiais devem ser adicionados manualmente nas <a href="edit.php?post_type=exhibition">exposições</a>.', 'iande') ?></li>
            </ul>
        </div><?php
    }
}
