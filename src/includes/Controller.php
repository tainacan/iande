<?php

use function Iande\set_404;
use function Iande\template_render;

/**
 * Classe base dos controladores
 */
abstract class Controller
{
    /**
     * Instance
     *
     * @var self
     */
    protected static $instance;

    /**
     * Retorna a instância do controlador
     *
     * @return self
     */
    static function get_instance()
    {
        if (!self::$instance) {
            $class = static::class;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    /**
     * Retorna o slug do controller
     *
     * @return string
     */
    function get_slug()
    {
        $class = (new \ReflectionClass($this))->getShortName();

        return strtolower($class);
    }

    /**
     * Call controller action.
     * 
     * if the 
     *
     * @param [type] $action
     * @param [type] $params
     * @return void
     */
    function call($action, $params)
    {
        if (\wp_is_json_request()) {
            $method = "endpoint_{$action}";
        } else {
            $method = "view_{$action}";
        }

        $controller = $this->get_slug();
        $params = \apply_filters("iande.route.{$controller}/{$action}.params", $params);

        \do_action("iande.route.{$controller}/{$action}", $params);

        if (method_exists($this, $method)) {
            $this->$method($params);

            \do_action("iande.route_not_found", $controller, $action, $params);

            set_404();
            return;
        }
    }

    /**
     * Verifica se o usuário está autenticado e 
     * se nao estiver renderiza mensagem de erro
     *
     * @param string $error_message
     * @return void
     */
    function require_authentication($error_message = '')
    {
        $error_message = $error_message ?: __('This action requires authentication');

        if (!is_user_logged_in()) {
            if (\wp_is_json_request()) {
                $this->error($error_message);
            } else {
                $this->render('access-denied', ['error_message' => $error_message]);
            }
        }
    }

    /**
     * Renderiza a visão
     *
     * @param string $template_name nome do template a ser renderizado
     * @param array $params variáveis para a visão
     * @param integer $http_status_code status da resposta http. default: 200
     * @return void
     */
    function render(string $template_name, array $params = [], $http_status_code = 200)
    {
        \status_header($http_status_code);
        template_render($template_name, $params);
        die;
    }

    /**
     * Imprime um json de resposta de erro
     *
     * @param mixed $data informações sobre o erro
     * @param integer $http_status_code status da resposta http. default: 400
     * @return void
     */
    protected function error($data, int $http_status_code = 400)
    {
        if ($http_status_code == 200) {
            throw new \Exception('error status could not be 200');
        }

        $this->json($data, $http_status_code);
    }

    /**
     * Imprime um json de resposta de sucesso
     *
     * @param mixed $data informações a serem retornadas no json
     * @param integer $http_status_code status da resposta http. default: 200
     * @return void
     */
    protected  function success($data, int $http_status_code = 200)
    {
        if ($http_status_code >= 300) {
            throw new \Exception('success status could not be ' . $http_status_code);
        }

        $this->json($data, $http_status_code);
    }

    /**
     * Imprime um json de resposta com o status http infomado.
     *
     * @param mixed $data informações a serem retornadas no json
     * @param integer $http_status_code
     * @return void
     */
    protected function json($data, int $http_status_code)
    {
        header('Content-Type: application/json');

        http_response_code($http_status_code);

        echo json_encode($data);

        die;
    }
}
