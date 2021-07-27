<?php
namespace IandePlugin;

function add_custom_roles_and_capabilities () {

    \add_role('iande_visitor', __('Visitante do Iandé', 'iande'));
    $subscriber = \get_role('iande_visitor');
    $subscriber->add_cap('upload_files');

    \add_role('iande_admin', __('Administrador do Iandé', 'iande'));

    // iande_admin
    set_iande_admin_capabilities('iande_admin', 'appointment', 'appointments');
    set_iande_admin_capabilities('iande_admin', 'exhibition', 'exhibitions');
    set_iande_admin_capabilities('iande_admin', 'institution', 'institutions');
    set_iande_admin_capabilities('iande_admin', 'exception', 'exceptions');
    set_iande_admin_capabilities('iande_admin', 'group', 'groups');
    set_iande_admin_capabilities('iande_admin', 'itinerary', 'itineraries');

    // administrator
    set_iande_admin_capabilities('administrator', 'appointment', 'appointments');
    set_iande_admin_capabilities('administrator', 'exhibition', 'exhibitions');
    set_iande_admin_capabilities('administrator', 'institution', 'institutions');
    set_iande_admin_capabilities('administrator', 'exception', 'exceptions');
    set_iande_admin_capabilities('administrator', 'group', 'groups');
    set_iande_admin_capabilities('administrator', 'itinerary', 'itineraries');

    // adiciona capability `read_feedback` à role `administrator`
    $administrator = \get_role('administrator');
    $administrator->add_cap('read_feedback');

}
\add_action('init', 'IandePlugin\\add_custom_roles_and_capabilities');

function set_iande_admin_capabilities($role, $singular, $plural) {

    $set_role = \get_role($role);

    $set_role->add_cap('edit_'.$singular);
    $set_role->add_cap('read_'.$singular);
    $set_role->add_cap('delete_'.$singular);
    $set_role->add_cap('edit_'.$plural);
    $set_role->add_cap('edit_others_'.$plural);
    $set_role->add_cap('publish_'.$plural);
    $set_role->add_cap('read_private_'.$plural);
    $set_role->add_cap('delete_'.$plural);
    $set_role->add_cap('delete_private_'.$plural);
    $set_role->add_cap('delete_published_'.$plural);
    $set_role->add_cap('delete_others_'.$plural);
    $set_role->add_cap('edit_private_'.$plural);
    $set_role->add_cap('edit_published_'.$plural);
    $set_role->add_cap('manage_iande_options');
    $set_role->add_cap('read');
    $set_role->add_cap('upload_files');

}