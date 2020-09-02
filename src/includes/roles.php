<?php
namespace IandePlugin;

function add_custom_roles_and_capabilities () {
    add_role('iande_admin', __('Administrador do Iandé', 'iande'), [
        'read' => true,
        'edit_appointments' => true,
        'publish_appointments' => true,
    ]);
}
\add_action('init', 'IandePlugin\\add_custom_roles_and_capabilities');