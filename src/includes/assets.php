<?php

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function enqueue_assets() {
    \wp_enqueue_style('iande', IANDE_PLUGIN_DISTURL . 'app.css', [], IANDE_PLUGIN_VERSION);
    \wp_enqueue_script('iande', IANDE_PLUGIN_DISTURL . 'app.js', ['wp-i18n'], IANDE_PLUGIN_VERSION, true);
}
