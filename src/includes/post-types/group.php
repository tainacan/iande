<?php

namespace IandePlugin;

add_action('init', 'IandePlugin\\register_post_type_group');
add_action('cmb2_admin_init', 'IandePlugin\\register_metabox_group');

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
        'all_items'          => __('Todos os Grupos', 'iande'),
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
        'show_in_menu'       => true,
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

    /* Registra os metadados do post type `group` */
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

    $metadata_definition = [

        'num_people' => (object) [
            'type'       => 'string',
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
            'required'   => true,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
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
            'required'   => true,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
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
            'type'       => 'string',
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
        // @todo aplicar repeater
        'languages' => (object) [
            'type'       => 'string',
            'required'   => false,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
                } else {
                    return __('O valor informado não é válido', 'iande');
                }
            },
            'metabox' => (object) [
                'name' => __('Idiomas', 'iande'),
                'type' => 'text',
            ]
        ],
        // @todo aplicar repeater
        'disabilities' => (object) [
            'type'       => 'string',
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
                'type'       => 'double_text',
                'repeatable' => true,
                'options'    => [
                    'name_1'       => __('Deficiência', 'iande'),
                    'name_2'       => __('Quantidade', 'iande'),
                    'add_row_text' => __('Adicionar Deficiência/Quantidade', 'iande')
                ]
            ]
        ]

    ];

    $metadata_definition = \apply_filters('iande.group_metadata_definition', $metadata_definition);

    return $metadata_definition;
}