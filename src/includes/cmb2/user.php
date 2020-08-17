<?php

add_action('cmb2_admin_init', 'iande_register_user_profile_metabox');

/**
 * Hook in and add a metabox to add fields to the user profile pages
 * 
 * @see https://cmb2.io/docs/basic-usage#-adding-metaboxes-to-user-profile
 */
function iande_register_user_profile_metabox()
{
    $prefix = 'iande_user_';

    /**
     * Metabox for the user profile screen
     */
    $cmb_user = new_cmb2_box([
        'id'               => $prefix . 'edit',
        'title'            => __('User Profile Metabox', 'iande'), // Doesn't output for user boxes
        'object_types'     => ['user'],
        'show_names'       => true,
        'new_user_section' => 'add-new-user'
    ]);

    $cmb_user->add_field([
        'name'     => __('Informações Adicionais', 'iande'),
        'desc'     => __('Informações necessárias para o Iandé Plugin', 'iande'),
        'id'       => $prefix . 'extra_info',
        'type'     => 'title',
        'on_front' => false
    ]);

    $cmb_user->add_field([
        'name' => __('Telefone', 'iande'),
        'desc' => __('Telefone com DDD', 'iande'),
        'id'   => 'phone',
        'type' => 'text'
    ]);

}
