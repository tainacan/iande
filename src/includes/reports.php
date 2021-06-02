<?php

namespace IandePlugin;

/**
 * Renderiza a página de relatórios
 */
function render_iande_reports_page () {
    ?>
    <div class="wrap">
        <h1><?= __('Relatórios', 'iande') ?></h1>

        <div id="iande-reports-app">
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
function parse_report_data ($post_type, $definitions) {
    global $wpdb;

    $posts_results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_type, post_status, post_author, post_date FROM $wpdb->posts WHERE post_type = %s", $post_type));
    $meta_results = $wpdb->get_results($wpdb->prepare("SELECT pm.post_id, pm.meta_key, pm.meta_value FROM $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.post_type = %s", $post_type));

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
 * Organiza os dados dos relatórios para o `wp_localize_script`
 */
function localize_reports() {

    $array = [
        'appointments' => parse_report_data('appointment', get_appointment_metadata_definition()),
        'exhibitions'  => parse_report_data('exhibition', get_exhibition_metadata_definition()),
        'groups'       => parse_report_data('group', get_all_group_metadata_definition()),
        'institutions' => parse_report_data('institution', get_institution_metadata_definition()),
    ];

    return \array_filter($array);

}

/**
 * Adiciona os assets da página de relatórios no admin
 */
function add_assets_reports() {
    \wp_enqueue_style( 'iande-reports-admin', IANDE_PLUGIN_DISTURL . 'reports.css', [] );
    \wp_enqueue_script('iande-reports-admin', IANDE_PLUGIN_DISTURL . 'reports.js', ['wp-i18n']);

    $localize_reports = localize_reports();

    if (!empty($localize_reports)) {
        wp_localize_script(
            'iande-reports-admin',
            'IandeReports',
            $localize_reports
        );
    }
}
