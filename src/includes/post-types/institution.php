<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_institution');
add_action('init', 'Iande\\register_metabox_institution');

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
 * Registra os metaboxes da instituição
 * 
 * @filter iande.institution_metabox_fields
 * 
 * @return void
 */
function register_metabox_institution()
{

    /* Registra os metaboxes do post type institution */

    $metadata_definition = get_institution_metadata_definition();

    $fields = [];
    $institution_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $institution_metabox = new \Iande_Metabox(
                'institution', // Slug/ID do Metabox (obrigatório)
                'Informações da Instituição', // Nome do Metabox  (obrigatório)
                'institution', // Slug do Post Type, sendo possível enviar apenas um valor ou um array com vários (opcional)
                'normal', // Contexto (opções: normal, advanced, ou side) (opcional)
                'high' // Prioridade (opções: high, core, default ou low) (opcional)
            );

            $label       = '';
            $type        = '';
            $default     = '';
            $description = '';
            $options     = [];
            $attributes  = [];

            if (isset($definition->metabox->label))
                $label = $definition->metabox->label;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->default))
                $default = $definition->metabox->default;

            if (isset($definition->metabox->description))
                $description = $definition->metabox->description;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            $fields[] = [
                'id'          => $key,
                'label'       => $label,
                'type'        => $type,
                'default'     => $default,
                'description' => $description,
                'options'     => $options,
                'attributes'  => $attributes
            ];

        }
        
    }

    $fields = \apply_filters('iande.institution_metabox_fields', $fields);

    if(is_object($institution_metabox))
        $institution_metabox->set_fields($fields);
    
    return $institution_metabox;

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

    $default_institution_profiles_option = [
        'Modelo de perfil 1',
        'Modelo de perfil 2',
        'Modelo de perfil 3'
    ];

    // @todo colocar em página de configuração
    $institution_profiles_option = get_option('iande_institution_profiles', $default_institution_profiles_option);

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
            },
            'metabox' => (object) [
                'label' => __('Nome', 'iande'),
                'type'  => 'text',
            ]
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
            },
            'metabox' => (object) [
                'label' => __('CNPJ', 'iande'),
                'type'  => 'text',
            ]
        ],
        'profile' => (object) [
            'type' => 'string',
            'required' => true,
            'validation' => function ($value) {
                if (is_string($value) || is_array($value)) {
                    return true;
                } else {
                    return __($value);
                }
            },
            'metabox' => (object) [
                'label'   => 'Perfil',
                'type'    => 'select',
                'options' => $institution_profiles_option
            ]
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