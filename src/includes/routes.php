<?php

namespace Iande;

use Controller;

/**
 * Define o status da requisição como 404
 *
 * @return void
 */
function set_404(){
    global $wp_query;
    $wp_query->set_404();
    \status_header(404);
}

/**
 * Adiciona os parâmetros necessários para o 
 * funcionamento das rotas do plugin
 */
\add_filter('query_vars', 'iande\\filter__query_vars');
function filter__query_vars(array $qvars)
{
    $qvars[] = 'iande_controller';
    $qvars[] = 'iande_action';
    return $qvars;
}

/**
 * Adiciona a regra de redirecionamento
 */
\add_action('init', 'iande\\action__rewrite_rules');
function action__rewrite_rules()
{
    \add_rewrite_rule('iande/([^/]+)/([^/]+)/?$', 'index.php?iande_controller=$matches[1]&iande_action=$matches[2]', 'top');
}

/**
 * Redireciona requisições como /iande/{controller}/{action}/ 
 * para a açao no controller: Iande\Controllers\{controller}::action_{action}
 */
\add_action('template_redirect', 'iande\\action__template_redirects');
function action__template_redirects()
{
    $controller_name = \get_query_var('iande_controller');
    $action = \get_query_var('iande_action');
    
    if (!$action || !$controller_name) {
        return;
    }

    require_once 'Controller.php';

    $controller_filename = IANDE_BASEPATH . 'controllers/' . strtolower($controller_name) . '.php';
    $controller_class = 'Iande\\' . ucfirst($controller_name);
    
    if (file_exists($controller_filename)) {
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
        $params = $_POST ?: $_GET;
    }

    $controller->call($action, $params);
}
