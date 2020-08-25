<?php

namespace Iande;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Adiciona 
 * 
 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
 */
function iande_activation() {

    add_option('iande_activation', '1');

}
register_activation_hook(IANDE_BASEPATH . 'iande.php', 'Iande\\iande_activation');

function iande_activation_plugin() {

    if (is_admin() && get_option('iande_activation') == '1') {

        delete_option('iande_activation');

        /**
         * Adicione aqui as funcões que deseja executar ao ativar o plugin
         */
        iande_cmb2_settings_init();

    }

}
add_action('admin_init', 'Iande\\iande_activation_plugin');

/**
 * Removes os styles padrões do tema
 * 
 * @link https://developer.wordpress.org/reference/functions/wp_dequeue_style/
 */
function iande_remove_default_stylesheet()
{

    global $wp_styles;

    foreach ( $wp_styles->queue as $style ) :

        if ($style != 'iande') {
            \wp_dequeue_style($style);
        }

    endforeach;

}
add_action('wp_enqueue_scripts', 'Iande\\iande_remove_default_stylesheet', 999);

/**
 * Removes os scripts padrões do tema
 * 
 * @link https://developer.wordpress.org/reference/functions/wp_dequeue_script/
 */
function iande_remove_default_scripts()
{

    global $wp_scripts;

    foreach ($wp_scripts->queue as $script) :

        if ($script != 'iande') {
            \wp_dequeue_script($script);
        }

    endforeach;

}
add_action('wp_enqueue_scripts', 'Iande\\iande_remove_default_scripts', 999999);