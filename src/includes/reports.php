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
