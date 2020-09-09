<?php
namespace IandePlugin;

function add_custom_roles_and_capabilities () {
    
    add_role('iande_admin', __('Administrador do Iandé', 'iande'));

    // iande_admin
    set_capabilities_to_role('iande_admin', 'appointment', 'appointments');
    set_capabilities_to_role('iande_admin', 'exhibition', 'exhibitions');
    set_capabilities_to_role('iande_admin', 'institution', 'institutions');
    set_capabilities_to_role('iande_admin', 'exception', 'exceptions');

    // administrator
    set_capabilities_to_role('administrator', 'appointment', 'appointments');
    set_capabilities_to_role('administrator', 'exhibition', 'exhibitions');
    set_capabilities_to_role('administrator', 'institution', 'institutions');
    set_capabilities_to_role('administrator', 'exception', 'exceptions');

}
\add_action('init', 'IandePlugin\\add_custom_roles_and_capabilities');

function set_capabilities_to_role($role, $singular, $plural) {

    $set_role = get_role($role);
    
    $set_role->add_cap('edit_'.$singular);
    $set_role->add_cap('read_'.$singular);
    $set_role->add_cap('delete_'.$singular);
    $set_role->add_cap('edit_'.$plural);
    $set_role->add_cap('edit_other_'.$plural);
    $set_role->add_cap('publish_'.$plural);
    $set_role->add_cap('read_private_'.$plural);
    $set_role->add_cap('delete_'.$plural);
    $set_role->add_cap('delete_private_'.$plural);
    $set_role->add_cap('delete_published_'.$plural);
    $set_role->add_cap('delete_others_'.$plural);
    $set_role->add_cap('edit_private_'.$plural);
    $set_role->add_cap('edit_published_'.$plural);

}