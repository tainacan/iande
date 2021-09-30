<?php
namespace IandePlugin;

function add_custom_roles_and_capabilities () {
    \add_role('iande_admin', __('Coordenador do Iandé', 'iande'));
    \add_role('iande_educator', __('Educador do Iandé', 'iande'));
    \add_role('iande_visitor', __('Visitante do Iandé', 'iande'));

    set_iande_capabilities('iande_visitor', [
        'capabilities' => ['upload_files'],
        'edit_posts' => false,
    ]);

    set_iande_capabilities('iande_educator', [
        'read' => ['exception', 'exhibition'],
        'manage' => ['appointment', 'group', 'institution', ['itinerary', 'itineraries']],
        'capabilities' => ['checkin', 'read', 'upload_files'],
        'edit_posts' => true,
    ]);

    set_iande_capabilities('iande_admin', [
        'manage' => ['appointment', 'exception', 'exhibition', 'group', 'institution', ['itinerary', 'itineraries']],
        'capabilities' => ['checkin', 'manage_iande_options', 'read', 'read_feedback', 'upload_files'],
        'edit_posts' => true,
    ]);

    set_iande_capabilities('administrator', [
        'manage' => ['appointment', 'exception', 'exhibition', 'group', 'institution', ['itinerary', 'itineraries']],
        'capabilities' => ['checkin', 'manage_iande_options', 'read', 'read_feedback', 'upload_files'],
        'edit_posts' => true,
    ]);
}
\add_action('init', 'IandePlugin\\add_custom_roles_and_capabilities');

function set_iande_capabilities (string $role, array $options) {
    $set_role = \get_role($role);

    if (!empty($options['read'])) {
        foreach ($options['read'] as $post_type) {
            $singular = $post_type;
            $plural = $post_type . 's';

            $set_role->add_cap('edit_'.$singular);
            $set_role->add_cap('edit_'.$plural);
            $set_role->add_cap('publish_'.$plural);
            $set_role->add_cap('read_'.$singular);
        }
    }

    if (!empty($options['manage'])) {
        foreach ($options['manage'] as $post_type) {
            if (is_array($post_type)) {
                $singular = $post_type[0];
                $plural = $post_type[1];
            } else {
                $singular = $post_type;
                $plural = $post_type . 's';
            }

            $set_role->add_cap('delete_'.$singular);
            $set_role->add_cap('delete_'.$plural);
            $set_role->add_cap('delete_others_'.$plural);
            $set_role->add_cap('delete_private_'.$plural);
            $set_role->add_cap('delete_published_'.$plural);
            $set_role->add_cap('edit_'.$singular);
            $set_role->add_cap('edit_'.$plural);
            $set_role->add_cap('edit_others_'.$plural);
            $set_role->add_cap('edit_private_'.$plural);
            $set_role->add_cap('edit_published_'.$plural);
            $set_role->add_cap('publish_'.$plural);
            $set_role->add_cap('read_'.$singular);
            $set_role->add_cap('read_private_'.$plural);
        }
    }

    foreach ($options['capabilities'] as $cap) {
        $set_role->add_cap($cap);
    }

    if ($options['edit_posts']) {
        $set_role->add_cap('edit_posts');
    } else {
        $set_role->remove_cap('edit_posts');
    }
}
