<?php

namespace IandePlugin;

\add_action('init', 'IandePlugin\\register_post_type_itinerary');

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
        'labels'             => $itinerary_labels,
        'description'        => __('Roteiros virtuais criados por educadores ou visitantes.', 'iande'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'iande-main-menu',
        'query_var'          => true,
        'rewrite'            => ['slug' => 'itinerary'],
        'capability_type'    => ['itinerary', 'itineraries'],
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-format-image',
        'supports'           => ['title', /* 'author', 'custom-fields' */]
    ];

    \register_post_type('itinerary', $itinerary_args);
}
