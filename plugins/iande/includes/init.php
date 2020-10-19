<?php

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Adiciona funções na ativação do plugin
 *
 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
 */
function iande_activation() {

    add_option('iande_activation', '1');

}
register_activation_hook(IANDE_PLUGIN_BASEPATH . 'iande.php', 'IandePlugin\\iande_activation');

function iande_activation_plugin() {

    if (is_admin() && get_option('iande_activation') == '1') {

        delete_option('iande_activation');

        /**
         * Adicione aqui as funcões que deseja executar ao ativar o plugin
         */
        iande_cmb2_settings_init();
        iande_settings_init();

    }

}
add_action('admin_init', 'IandePlugin\\iande_activation_plugin');


/**
 * Verifica se o plugin WP Mail SMTP by WPForms está ativo
 */
function check_dependencies() {
    
    if (!is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')) {
        echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>O plugin <b>WP Mail SMTP by WPForms</b> é necessário o envio de e-mails dos agendamentos do plugin <b>Iandé</b>. <a href="' . admin_url('/plugin-install.php?s=WP+Mail+SMTP&tab=search&type=term') . '">Clique aqui para instalá-lo</a>!</p>';
        echo '</div>';
    }

}
add_action('admin_notices', 'IandePlugin\\check_dependencies');

/**
 * Remove os styles padrões do tema
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
\add_action('wp_enqueue_scripts', 'IandePlugin\\iande_remove_default_stylesheet', 999999);

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
\add_action('wp_enqueue_scripts', 'IandePlugin\\iande_remove_default_scripts', 999999);


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
\add_filter('show_admin_bar', 'IandePlugin\\iande_remove_wp_admin_bar');


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

/**
 * Configurações iniciais do plugin Iandé
 */
function iande_settings_init() {

    /**
     * Cria exposição Acervo Permanente
     */
    $title = __('Acervo Permanente', 'iande');
    $exhibition_check = get_page_by_title($title, 'OBJECT', 'exhibition');
    $exhibition = array(
        'post_type'     => 'exhibition',
        'post_title'    => $title,
        'post_status'   => 'publish',
        'post_author'   => 1,
    );

    if (!isset($exhibition_check->ID)) {
        $exhibition_id = wp_insert_post($exhibition);

        $hm = '3' * 60;
        $ms = $hm * 60;
        $gmdata = gmdate("Y-m-d", time() - ($ms));

        update_post_meta($exhibition_id, 'date_from', $gmdata);

    }

    /**
     * Remove rewrite rules and then recreate.
     *
     * @link https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
     */
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');

    \flush_rewrite_rules();

}