<?php

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function mtime($filename)
{
    filemtime(IANDE_PLUGIN_BASEPATH . 'dist/' . $filename);
}

function enqueue_assets()
{
    \wp_enqueue_style('iande', IANDE_PLUGIN_DISTURL . 'app.css', [], mtime('app.css'));
    \wp_enqueue_script('iande', IANDE_PLUGIN_DISTURL . 'app.js', [], mtime('app.js'), true);
}