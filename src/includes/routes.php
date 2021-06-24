<?php

namespace IandePlugin;

use Controller;

/**
 * Define o status da requisição como 404
 *
 * @return void
 */
function set_404()
{
    global $wp_query;
    $wp_query->set_404();
    \status_header(404);
}

/**
 * Adiciona os parâmetros necessários para o
 * funcionamento das rotas do plugin
 */
\add_filter('query_vars', 'IandePlugin\\filter__query_vars');
function filter__query_vars(array $qvars)
{
    $qvars[] = 'iande_controller';
    $qvars[] = 'iande_action';
    return $qvars;
}

/**
 * Adiciona a regra de redirecionamento
 */
\add_action('init', 'IandePlugin\\action__rewrite_rules');
function action__rewrite_rules()
{
    \add_rewrite_rule('iande/([^/]+)/([^/]+)/?$', 'index.php?iande_controller=$matches[1]&iande_action=$matches[2]', 'top');
    \add_rewrite_rule('iande/?$', 'index.php?iande_controller=user&iande_action=welcome', 'top');
}

/**
 * Redireciona requisições como /iande/{controller}/{action}/
 * para a açao no controller: IandePlugin\Controllers\{controller}::action_{action}
 */
\add_action('template_redirect', 'IandePlugin\\action__template_redirects');
function action__template_redirects()
{
    $controller_name = \get_query_var('iande_controller');
    $action = \get_query_var('iande_action');
    $action = \str_replace('-', '_', $action);

    if (!$action || !$controller_name) {
        return;
    }

    require_once 'Controller.php';

    $controller_filename = IANDE_PLUGIN_BASEPATH . 'controllers/' . strtolower($controller_name) . '.php';
    $controller_class = 'IandePlugin\\' . ucfirst($controller_name);

    if ($controller_filename === realpath($controller_filename) && file_exists($controller_filename)) {
        require_once $controller_filename;
    } else {
        set_404();
        return;
    }

    if (!class_exists($controller_class)) {
        set_404();
        return;
    }

    if (!is_subclass_of($controller_class, Controller::class)) {
        set_404();
        return;
    }

    $controller = $controller_class::get_instance();

    enqueue_assets();

    if (!($params = (array) json_decode(file_get_contents('php://input')))) {
        $params = \filter_input_array(\INPUT_POST) ?: \filter_input_array(\INPUT_GET) ?: [];
    }

    $controller->call($action, $params);
}
