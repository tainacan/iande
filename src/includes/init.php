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

    if (is_iande_page()) {

        global $wp_styles;

        foreach ( $wp_styles->queue as $style ) :

            if ($style != 'iande') {
                \wp_dequeue_style($style);
            }

        endforeach;

    }

}
\add_action('wp_enqueue_scripts', 'Iande\\iande_remove_default_stylesheet', 999999);

/**
 * Removes os scripts padrões do tema
 *
 * @link https://developer.wordpress.org/reference/functions/wp_dequeue_script/
 */
function iande_remove_default_scripts()
{

    if(is_iande_page()) {
        global $wp_scripts;

        foreach ($wp_scripts->queue as $script) :

            if ($script != 'iande') {
                \wp_dequeue_script($script);
            }

        endforeach;
    }

}
\add_action('wp_enqueue_scripts', 'Iande\\iande_remove_default_scripts', 999999);


/**
 * Remove a WP Admin Bar das páginas do Iandé
 */
function iande_remove_wp_admin_bar($value)
{
    if (is_iande_page()) {
        return false;
    }
    return $value;
}
\add_filter('show_admin_bar', 'Iande\\iande_remove_wp_admin_bar');


/**
 * Condicional para verificar se é uma página do Iandé
 */
function is_iande_page()
{

    $controller_name = \get_query_var('iande_controller');
    $action = \get_query_var('iande_action');

    if (!$action || !$controller_name) {
        return;
    }

    return true;
}