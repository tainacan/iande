<?php

namespace IandePlugin;

\add_action('init', 'IandePlugin\\register_post_type_itinerary');
\add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_itinerary');

/**
 * Registra o Post Type Itinerary
 */
function register_post_type_itinerary() {
    $itinerary_labels = [
        'name'               => _x('Roteiros', 'post type general name', 'iande'),
        'singular_name'      => _x('Roteiro', 'post type singular name', 'iande'),
        'menu_name'          => _x('Roteiros', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Roteiro', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar novo', 'roteiro', 'iande'),
        'add_new_item'       => __('Adicionar Novo Roteiro', 'iande'),
        'new_item'           => __('Novo Roteiro', 'iande'),
        'edit_item'          => __('Editar Roteiro', 'iande'),
        'view_item'          => __('Ver Roteiro', 'iande'),
        'all_items'          => _x('Roteiros', 'all items', 'iande'),
        'search_items'       => __('Buscar Roteiro', 'iande'),
        'parent_item_colon'  => __('Roteiros Pais:', 'iande'),
        'not_found'          => __('Nenhum roteiro encontrado.', 'iande'),
        'not_found_in_trash' => __('Nenhum roteiro encontrado na lixeira.', 'iande')
    ];

    $itinerary_args = [
        'labels'              => $itinerary_labels,
        'description'         => __('Roteiros virtuais criados por educadores ou visitantes.', 'iande'),
        'public'              => true,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => 'iande-main-menu',
        'query_var'           => true,
        'rewrite'             => ['slug' => 'itinerary'],
        'capability_type'     => ['itinerary', 'itineraries'],
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-format-image',
        'supports'            => ['title', /* 'author', 'custom-fields' */]
    ];

    \register_post_type('itinerary', $itinerary_args);
}

/**
 * Registra os metaboxes do roteiro com CMB2
 *
 * @filter iande.itinerary_metabox_fields
 *
 * @return void
 */
function register_metabox_itinerary() {

    /* Registra os metaboxes do post type itinerary */

    $metadata_definition = get_itinerary_metadata_definition();

    $itinerary_metabox = \new_cmb2_box(array(
        'id'            => 'itinerary',
        'title'         => __('Informações do Roteiro', 'iande'),
        'object_types'  => ['itinerary'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true
    ));

    $fields = [];

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            /**
             * Fields parameters
             *
             * @link https://cmb2.io/docs/field-parameters
             */
            $name       = '';
            $desc       = '';
            $default    = '';
            $type       = '';
            $options    = [];
            $attributes = [];
            $repeatable = false;
            $size       = '';

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

            if (isset($definition->metabox->size))
                $size = $definition->metabox->size;

            $fields[] = [
                'name'       => $name,
                'desc'       => $desc,
                'id'         => $key,
                'default'    => $default,
                'type'       => $type,
                'options'    => $options,
                'attributes' => $attributes,
                'repeatable' => $repeatable,
                'size'       => $size
            ];

        }

    }

    $fields = \apply_filters('iande.itinerary_metabox_fields', $fields);

    if (\is_object($itinerary_metabox)) {
        foreach ($fields as $field) {
            $itinerary_metabox->add_field($field);
        }
    }

    return $itinerary_metabox;
}

/**
 * Retorna a definição dos metadados do post type `itinerary`
 *
 * @filter iande.itinerary_metadata_definition
 *
 * @return array
 */
function get_itinerary_metadata_definition() {

    $exhibitions = \get_posts([
        'post_type'      => 'exhibition',
        'post_status'    => ['publish'],
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'ID'
    ]);

    $collections = \get_posts([
        'post_type'      => 'tainacan-collection',
        'post_status'    => ['publish'],
        'posts_per_page' => -1,
        'order'          => 'ASC',
        'orderby'        => 'ID'
    ]);

    $metadata_definition = [
        'name' => (object) [
            'type'       => 'string',
            'required'   => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Nome', 'iande'),
                'type' => 'text'
            ]
        ],
        'source' => (object) [
            'type'       => 'string',
            'required'   => true,
            'validation' => function ($value) {
                if (\in_array($value, ['all', 'collection', 'exhibition'])) {
                    return true;
                } else {
                    return __('Valor inválido para fonte', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Como montar o roteiro', 'iande'),
                'type'    => 'select',
                'default' => 'all',
                'options' => [
                    'exhibition' => __('A partir de uma exposição', 'iande'),
                    'collection' => __('A partir de uma coleção específica', 'iande'),
                    'all'        => __('A partir do repositório completo de itens do museu', 'iande'),
                ]
            ]
        ],
        'exhibition' => (object) [
            'type' =>     'integer',
            'required' => false,
            'validation' => function ($value, $params) use ($exhibitions) {
                if ($params['source'] !== 'exhibition') {
                    return true;
                } else if (is_numeric($value) && intval($value) == $value) {
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
        'collection' => (object) [
            'type' =>     'integer',
            'required' => false,
            'validation' => function ($value, $params) use ($collections) {
                if ($params['source'] !== 'collection') {
                    return true;
                } else if (is_numeric($value) && intval($value) == $value) {
                    if (array_post_exists($value, $collections)) {
                        return true;
                    } else {
                        return __('Coleção inválida', 'iande');
                    }
                } else {
                    return __('O valor informado para a coleção deve ser um inteiro', 'iande');
                }
            },
            'metabox' => (object) [
                'name'    => __('Coleção', 'iande'),
                'type'    => 'select',
                'options' => map_posts_to_options($collections),
            ]
        ],
    ];

    $metadata_definition = \apply_filters('iande.itinerary_metadata_definition', $metadata_definition);

    return $metadata_definition;
}