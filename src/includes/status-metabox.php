<?php

function appointment_enqueue_scripts() {
    wp_enqueue_style( 'iande-admin', IANDE_PLUGIN_DISTURL . 'admin.css', [] );
    wp_enqueue_script( 'iande-admin', IANDE_PLUGIN_DISTURL . 'admin.js' );

    $site_url         = get_bloginfo('url');
    $iande_url        = get_site_url(null, '/iande');
    $duration         = cmb2_get_option('iande_appointments_settings', 'duration', []);
    $group_size       = cmb2_get_option('iande_appointments_settings', 'group_size', []);
    $group_slot       = cmb2_get_option('iande_appointments_settings', 'group_slot', []);
    $grid             = cmb2_get_option('iande_appointments_settings', 'grid', []);
    $schedules        = get_option('iande_schedules', []);
    $schedules        = array_filter($schedules);

    wp_localize_script(
        'iande-admin',
        'IandeSettings',
        [
            'siteUrl'           => $site_url,
            'iandeUrl'          => $iande_url,
            'duration'          => $duration,
            'groupSize'         => $group_size,
            'groupSlot'         => $group_slot,
            'grid'              => $grid,
            'schedules'         => $schedules,
        ]
    );
}
add_action( 'admin_enqueue_scripts', 'appointment_enqueue_scripts' );

/**
 * Add metabox on panel
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function status_metaboxes($post)
{
    add_meta_box('status_metaboxes', __('Status', 'iande'), 'build_status_metabox', 'appointment', 'side', 'low');
}
add_action('add_meta_boxes_appointment', 'status_metaboxes');

/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
function build_status_metabox($post)
{
?>
    <div class="iande-admin-app">
        <iande-status-metabox id="<?php echo get_the_ID(); ?>"></iande-status-metabox>
    </div>
<?php
}

/**
 * Save metabox data
 *
 * @param int $post_id The post ID.
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
 */
function save_status_metabox($post_id)
{
}
add_action('save_post_appointment', 'save_status_metabox');
