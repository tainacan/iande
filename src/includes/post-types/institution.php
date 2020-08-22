<?php

namespace Iande;

add_action('init', 'Iande\\register_post_type_institution');
add_action('cmb2_admin_init', 'Iande\\register_metabox_institution');

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
 * RRegistra os metaboxes do agendamento com CMB2
 * 
 * @filter iande.institution_metabox_fields
 * 
 * @return void
 */
function register_metabox_institution() {

    /* Registra os metaboxes do post type institution */

    $metadata_definition = get_institution_metadata_definition();

    $fields = [];
    $institution_metabox = '';

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $appointment_metabox = \new_cmb2_box(array(
                'id'            => 'institution',
                'title'         => __('Informações do Agendamento', 'iande'),
                'object_types'  => array('institution'),
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

    $fields = \apply_filters('iande.institution_metabox_fields', $fields);

    if (is_object($appointment_metabox)) {
        foreach ($fields as $field) {
            $appointment_metabox->add_field($field);
        }
    }

    return $appointment_metabox;

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
        'Escola estadual',
        'Escola municipal',
        'Escola federal',
        'Escola privada',
        'Universidade pública',
        'Universidade/faculdade privada',
        'ONG',
        'Agência de turismo',
        'Empresa',
        'Outros'
    ];

    // @todo colocar em página de configuração
    $institution_profiles_option = get_option('iande_institution_profiles', $default_institution_profiles_option);

    $institution_scholarity = get_option('iande_institution', []);
    if (array_key_exists('institution_scholarity', $institution_scholarity)) {
        $institution_scholarity = $institution_scholarity['institution_scholarity'];
    } else {
        $institution_scholarity = [];
    }

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
                'name' => __('Nome', 'iande'),
                'type' => 'text',
                'size' => '50' // 75%, 50%, 33%, 25%, default 100%
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
                'name' => __('CNPJ', 'iande'),
                'type'  => 'text',
                'size'  => '50'
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
                'name'   => 'Perfil',
                'type'    => 'select',
                'options' => $institution_profiles_option,
                'size'  => '50'
            ]
        ],
        'scholarity' => (object) [
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
                'name'   => 'Escolaridade',
                'type'    => 'select',
                'options' => $institution_scholarity,
                'size'  => '50'
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
            },
            'metabox' => (object) [
                'name' => __('Telefone', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('E-mail', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('CEP', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('Endereço', 'iande'),
                'type'  => 'text',
                'size'  => '75'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('Número', 'iande'),
                'type'  => 'text',
                'size'  => '25'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('Complemento', 'iande'),
                'type'  => 'text',
                'description' => 'Vamos ver aqui como fica uma descrição para os campos',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('Bairro', 'iande'),
                'type'  => 'text',
                'size'  => '50'
            ]
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
            },
            'metabox' => (object) [
                'name' => __('Cidade', 'iande'),
                'type'  => 'text',
                'size'  => '75'
            ]

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
            },
            'metabox' => (object) [
                'name' => __('Estado', 'iande'),
                'type'  => 'text',
                'description' => 'Vamos ver aqui como fica uma descrição para os campos',
                'size'  => '25'
            ]
        ]
    ];

    $metadata_definition = \apply_filters('iande.institution_metadata_definition', $metadata_definition);

    return $metadata_definition;
}