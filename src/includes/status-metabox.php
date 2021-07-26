<?php

function appointment_enqueue_scripts() {
    wp_enqueue_style('iande-admin', IANDE_PLUGIN_DISTURL . 'admin.css', [], IANDE_PLUGIN_VERSION);
    wp_enqueue_script('iande-admin', IANDE_PLUGIN_DISTURL . 'admin.js', ['wp-i18n'], IANDE_PLUGIN_VERSION, true);

    $site_url         = get_bloginfo('url');
    $iande_url        = get_site_url(null, '/iande');
    $tainacan_url     = get_site_url(null, '/wp-json/tainacan/v2');
    $purposes         = cmb2_get_option('iande_appointments_settings', 'appointment_purpose', []);
    $profiles         = cmb2_get_option('iande_institution', 'institution_profile', []);
    $responsible_role = cmb2_get_option('iande_institution', 'institution_responsible_role', []);
    $deficiency       = cmb2_get_option('iande_institution', 'institution_deficiency', []);
    $language         = cmb2_get_option('iande_institution', 'institution_language', []);
    $age_range        = cmb2_get_option('iande_institution', 'institution_age_range', []);
    $scholarity       = cmb2_get_option('iande_institution', 'institution_scholarity', []);

    wp_localize_script(
        'iande-admin',
        'IandeSettings',
        [
            'siteUrl'          => $site_url,
            'iandePath'        => IANDE_PLUGIN_BASEURL,
            'iandeUrl'         => $iande_url,
            'tainacanUrl'      => $tainacan_url,
            'profiles'         => $profiles,
            'purposes'         => $purposes,
            'responsibleRoles' => $responsible_role,
            'disabilities'     => $deficiency,
            'languages'        => $language,
            'ageRanges'        => $age_range,
            'scholarity'       => $scholarity,
        ]
    );
}
add_action('admin_enqueue_scripts', 'appointment_enqueue_scripts');

/**
 * Add metabox on panel
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function status_metabox($post) {
    if ($post->post_status === 'pending' || $post->post_status === 'publish') {
        add_meta_box('status_metabox', __('Status', 'iande'), 'build_status_metabox', 'appointment', 'side', 'low');
    }
}
add_action('add_meta_boxes_appointment', 'status_metabox');

/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
function build_status_metabox($post) {
?>
    <div class="iande-admin-app">
        <iande-status-metabox id="<?php echo get_the_ID(); ?>" post-status="<?php echo get_post_status($post) ?>"></iande-status-metabox>
    </div>
<?php
}
