<?php

function iande_polyfills () {
    if (!is_admin()) {
        if (!function_exists('is_plugin_active')) {
            function is_plugin_active($plugin) {
                return in_array($plugin, (array) get_option('active_plugins', []));
            }
        }
    }
}
add_action('init', 'iande_polyfills');
