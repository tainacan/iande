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
