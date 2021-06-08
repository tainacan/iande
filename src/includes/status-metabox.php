<?php

function appointment_enqueue_scripts() {
    wp_enqueue_style( 'iande-admin', IANDE_PLUGIN_DISTURL . 'admin.css', [] );
    wp_enqueue_script( 'iande-admin', IANDE_PLUGIN_DISTURL . 'admin.js', ['wp-i18n'] );

    $site_url         = get_bloginfo('url');
    $iande_url        = get_site_url(null, '/iande');

    wp_localize_script(
        'iande-admin',
        'IandeSettings',
        [
            'siteUrl'           => $site_url,
            'iandeUrl'          => $iande_url,
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
    if ($post->post_status === 'pending' || $post->post_status === 'publish') {
        add_meta_box('status_metaboxes', __('Status', 'iande'), 'build_status_metabox', 'appointment', 'side', 'low');
    }
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
        <iande-status-metabox id="<?php echo get_the_ID(); ?>" post-status="<?php echo get_post_status($post) ?>"></iande-status-metabox>
    </div>
<?php
}
