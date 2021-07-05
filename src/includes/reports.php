<?php

namespace IandePlugin;

/**
 * Renderiza a página de relatórios
 */
function render_iande_reports_page () {
    ?>
    <div class="wrap" id="iande-reports-page">
        <h1><?php echo \esc_html__('Relatórios', 'iande') ?></h1>

        <div class="iande-admin-app">
            <iande-reports-page></iande-reports-page>
        </div>
    </div>
    <?php
}

/**
 * Retorna todos os posts de um post_type, devidamente serializados
 *
 * @param string $post_type O 'post_type' escolhido
 * @param array $definitions As definições dos campos do post_type
 * @return array A array de posts
 */
function serialize_post_type ($post_type, $definitions) {
    global $wpdb;

    $post_status = ['publish', 'pending', 'canceled'];
    $placeholders = array_fill(0, count( $post_status ), '%s');
    $in_post_status = implode(', ', $placeholders);
    $prepared_values = array_merge([$post_type], $post_status);

    $posts_results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_type, post_status, post_author, post_date FROM $wpdb->posts WHERE post_type = %s AND post_status IN ($in_post_status)", $prepared_values));
    $meta_results = $wpdb->get_results($wpdb->prepare("SELECT pm.post_id, pm.meta_key, pm.meta_value FROM $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.post_type = %s AND post_status IN ($in_post_status)", $prepared_values));

    $posts = [];
    foreach ($posts_results as $post) {
        $posts[$post->ID] = $post;
    }

    foreach ($meta_results as $meta) {
        $postId = $meta->post_id;
        $metaKey = $meta->meta_key;
        if (\key_exists($postId, $posts) && \key_exists($metaKey, $definitions)) {
            $posts[$postId]->$metaKey = \maybe_unserialize($meta->meta_value);
        }
    }

    return \array_values($posts);
}

/**
 * Organiza os dados dos relatórios
 *
 * @return array
 */
function prepare_report_data () {
    return [
        'appointments' => serialize_post_type('appointment', get_appointment_metadata_definition()),
        'exhibitions'  => serialize_post_type('exhibition', get_exhibition_metadata_definition()),
        'groups'       => serialize_post_type('group', get_all_group_metadata_definition()),
        'institutions' => serialize_post_type('institution', get_institution_metadata_definition()),
    ];
}

/**
 * Localiza os assets da página de relatórios no admin
 *
 * @return void
 */
function localize_reports_assets () {
    \wp_localize_script('iande-admin', 'IandeReports', prepare_report_data());
}
