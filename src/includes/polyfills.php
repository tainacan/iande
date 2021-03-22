<?php

if (!function_exists('is_plugin_active')) {
    function is_plugin_active($plugin) {
        return in_array($plugin, (array) get_option('active_plugins', []));
    }
}
