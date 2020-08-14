<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_institution');

/**
 * Registra o Post Type Institution
 */
function register_post_type_institution()
{
    $institution_labels = [
        'name'               => _x('Instituições', 'post type general name', 'iande'),
        'singular_name'      => _x('Instituição', 'post type singular name', 'iande'),
        'menu_name'          => _x('Instituições', 'admin menu', 'iande'),
        'name_admin_bar'     => _x('Instituição', 'add new on admin bar', 'iande'),
        'add_new'            => _x('Adicionar nova', 'instituição', 'iande'),
        'add_new_item'       => __('Adicionar Nova Instituição', 'iande'),
        'new_item'           => __('Novo Instituição', 'iande'),
        'edit_item'          => __('Editar Instituição', 'iande'),
        'view_item'          => __('Ver Instituição', 'iande'),
        'all_items'          => __('Todos os Instituições', 'iande'),
        'search_items'       => __('Buscar Instituições', 'iande'),
        'parent_item_colon'  => __('Instituições Pais:', 'iande'),
        'not_found'          => __('Nenhuma instituição encontrada.', 'iande'),
        'not_found_in_trash' => __('Nenhuma instituição encontrada na lixeira.', 'iande')
    ];

    $institution_args = [
        'labels'             => $institution_labels,
        'description'        => __('Instituições adicionadas pelos usuários.', 'iande'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'institution'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-building',
        'supports'           => ['title', 'author', /* 'custom-fields' */]
    ];

    register_post_type('institution', $institution_args);


    /* Registra os metadados do post type institution */

    $metadata_definition = get_institution_metadata_definition();

    foreach ($metadata_definition as $key => $definition) {
        register_post_meta('institution', $key, ['type' => $definition->type]);
    }
}


/**
 * Retorna a definição dos metadados do post type `institution`
 *
 * @filter iande.institution_metadata_definition
 *
 * @return array
 */
function get_institution_metadata_definition()
{

    $metadata_definition = [
        'name' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O nome informado é muito curto', 'iande');
                }
            }
        ],
        'cnpj' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) == 14 && is_numeric($value)) {
                    return true;
                } else {
                    return __('O número informado não é um CNPJ válido', 'iande');
                }
            }
        ],
        'profile' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (is_string($value) && !is_numeric($value)) {
                    return true;
                } else {
                    return __('O perfil informado não é um perfil válido', 'iande');
                }
            }
        ],
        'phone' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (is_numeric($value)) {
                    return true;
                } else {
                    return __('O número informado não é um telefone válido', 'iande');
                }
            }
        ],
        'email' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (is_email($value)) {
                    return true;
                } else {
                    return __('O e-mail informado não é um e-mail válido', 'iande');
                }
            }
        ],
        'zip_code' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) == 8 && is_numeric($value)) {
                    return true;
                } else {
                    return __('O CEP informado não é válido', 'iande');
                }
            }
        ],
        'address' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O endereço informado não é válido', 'iande');
                }
            }
        ],
        'address_number' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (is_string($value)) {
                    return true;
                } else {
                    return __('O número informado não é válido', 'iande');
                }
            }
        ],
        'complement' => (object) [
            'type' => 'string',
            'required' => false,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O complemento informado não é válido', 'iande');
                }
            }
        ],
        'district' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('O bairro informado não é válido', 'iande');
                }
            }
        ],
        'state' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) == 2) {
                    return true;
                } else {
                    return __('O estado informado não é válido', 'iande');
                }
            }
        ],
        'city' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (strlen(trim($value)) >= 2) {
                    return true;
                } else {
                    return __('A cidade informada não é válida', 'iande');
                }
            }
        ]
    ];

    $metadata_definition = \apply_filters('iande.institution_metadata_definition', $metadata_definition);

    return $metadata_definition;
}
