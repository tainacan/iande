<?php

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Indicates if a Iandé route was successfully matched
 * @var bool
 */
$iande_success = false;

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
    global $iande_success;

    if ($iande_success) {

        global $wp_styles;

        $allowedStyles = ['tainacan-fonts'];

        foreach ( $wp_styles->queue as $style ) :

            if (!in_array($style, $allowedStyles) && strpos($style, 'iande') === false) {
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
    global $iande_success;

    if($iande_success) {
        global $wp_scripts;

        $allowedScripts = ['tainacan-search', 'wp-i18n'];

        foreach ($wp_scripts->queue as $script) :

            if (!in_array($script, $allowedScripts) && strpos($script, 'iande') === false) {
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

/**
 * Adiciona entrada para Iandé no menu do WP-Admin
 */
function add_iande_menu () {
    $icon = IANDE_PLUGIN_BASEURL . '/assets/img/iande-menu-icon.svg';
    // $icon = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(IANDE_PLUGIN_BASEURL . '/assets/img/iande-menu-icon-pb.svg'));

    \add_menu_page('Iandé', 'Iandé', 'manage_iande_options', 'iande-main-menu', '', $icon, 100);
    \add_submenu_page('iande-main-menu', '', __('Check-in', 'iande'), 'manage_iande_options', 'iande_checkin_frontend', '__');
    \add_submenu_page('iande-main-menu', '', __('Front-end', 'iande'), 'read', 'iande_frontend', '__');
}
\add_action('admin_menu', 'IandePlugin\\add_iande_menu');

/**
 * Redireciona algumas páginas do plugin para o front-end do Iandé
 */
function redirect_to_iande_frontend () {
    $menu_redirect = isset($_GET['page']) ? \filter_var($_GET['page']) : '';

    if ($menu_redirect === 'iande_frontend') {
        \wp_safe_redirect(get_site_url(null, '/iande/?force_view=visitor'));
        exit;
    } else if ($menu_redirect === 'iande_checkin_frontend') {
        \wp_safe_redirect(get_site_url(null, '/iande/group/list/?force_view=educator'));
        exit;
    }
}
\add_action( 'admin_init', 'IandePlugin\\redirect_to_iande_frontend', 1 );

/**
 * Redireciona usuários com a role `iande_admin` para a área administrativa do plugin
 */
function login_redirect_iande_admin( $redirect_to, $request, $user )
{
	return ( isset( $user->roles ) && is_array( $user->roles ) && in_array( 'iande_admin', $user->roles ) ) ? \admin_url( 'edit.php?post_type=appointment' ) : \admin_url();
}
\add_filter( 'login_redirect', 'IandePlugin\\login_redirect_iande_admin', 10, 3 );

/**
 * Redireciona usuários com a role `iande_admin` para o front end  `/iande/ `após logout
 */
function logout_redirect_iande_admin( $redirect_to, $requested_redirect_to, $user )
{
    return ( is_array( $user->roles ) && in_array( 'iande_admin', $user->roles ) ) ? \home_url( '/iande/' ) : $redirect_to;
}
\add_filter( 'logout_redirect', 'IandePlugin\\logout_redirect_iande_admin', 10, 3);