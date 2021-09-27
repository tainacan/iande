<?php

use function IandePlugin\set_404;
use function IandePlugin\template_render;

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
     * @param string $action The requested view or method
     * @param array $params
     * @return void
     */
    function call($action, $params)
    {
        global $iande_success;

        if (\wp_is_json_request()) {
            $method = "endpoint_{$action}";
        } else {
            $method = "view_{$action}";
        }

        $controller = $this->get_slug();
        $params = \apply_filters("iande.route.{$controller}/{$action}.params", $params);

        \do_action("iande.route.{$controller}/{$action}", $params);

        if (method_exists($this, $method)) {
            $iande_success = true;
            $this->$method($params);

            $iande_success = false;
            \do_action("iande.route_not_found", $controller, $action, $params);

            set_404();
            return;
        }
    }

    /**
     * Verifica se o usuário está autenticado e tem a capability,
     * se não renderiza mensagem de erro
     *
     * @param string $capability
     * @param string $error_message
     * @return void
     */
    function require_credentials($capability = 'checkin', $error_message = '') {
        $this->require_authentication($error_message);

        if (!\current_user_can($capability)) {
            if (\wp_is_json_request()) {
                $error_message = $error_message ?: __('Essa ação requer privilégios administrativos', 'iande');
                $this->error($error_message, 403);
            } else {
                $this->render('login', ['next' => $_SERVER['REQUEST_URI']]);
            }
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
        if (!\is_user_logged_in()) {
            if (\wp_is_json_request()) {
                $error_message = $error_message ?: __('Essa ação requer autenticação', 'iande');
                $this->error($error_message, 401);
            } else {
                $this->render('login', ['next' => $_SERVER['REQUEST_URI']]);
            }
        }
    }

    /**
     * Renderiza a view
     *
     * @param string $template_name nome do template a ser renderizado
     * @param array $params variáveis para a view
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
     * Renderiza a view padrão do Iandé
     *
     * @param string $title título da página
     * @param string $component nome do componente a ser renderizado na página
     * @param integer $http_status_code status da resposta http. default: 200
     * @return void
     */
    function render_vue(string $title, string $component, $http_status_code = 200)
    {
        \status_header($http_status_code);
        template_render('component', [ 'title' => $title, 'component' => $component ]);
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
        header('Vary: Accept');

        http_response_code($http_status_code);

        echo json_encode($data);

        die;
    }

    /**
     * Envia e-mail de transição do plugin.
     *
     * @return void
     */
    protected function email(string $email_template, array $params)
    {

        if (empty($params['email'])) {
            $this->error(__('O endereço de e-mail é obrigatório', 'iande'));
        }

        $body = '';
        $subject = '';

        $emails_settings = \get_option('iande_emails_settings', '');

        if (!empty($emails_settings) && isset($emails_settings[$email_template])) {
            $body = $emails_settings[$email_template];
        }

        if (!empty($emails_settings) && isset($emails_settings[$email_template . '_title'])) {
            $subject = $emails_settings[$email_template . '_title'];
        }

        $headers = [];
        if (isset($params['headers']) && !empty($params['headers'])) {
            $headers[] = $params['headers'];
        } else {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        }

        if (isset($params['cc']) && $params['email'] !== $params['cc']) {
            $headers[] = 'Cc: ' . $params['cc'];
        }

        $attachments = '';
        if (!empty($emails_settings) && isset($emails_settings[$email_template . '_attachment'])) {
            $attachments = \get_attached_file( $emails_settings[$email_template . '_attachment_id']);
        }

        // executa as interpolações no título e corpo do e-mail
        if (!empty($params['interpolations'])) {

            foreach ($params['interpolations'] as $keyword => $value) {

                if ($keyword == 'grupos') {

                    $interpolation_group = [];
                    foreach ($value as $group_id) {

                        $interpolation_group[] = "\n<b>" . \get_post_meta($group_id, 'name', true) . "</b>";
                        $interpolation_group[] = "<b>Data:</b> " . date('d/m/Y', strtotime(\get_post_meta($group_id, 'date', true))) . " / <b>Hora:</b> " . \get_post_meta($group_id, 'hour', true) . "\n";

                    }

                    $interpolation_group = implode("\n", $interpolation_group);

                    $body = str_replace('%' . $keyword . '%', $interpolation_group, $body);

                } else {
                    $body    = str_replace('%' . $keyword . '%', $value, $body);
                    $subject = str_replace('%' . $keyword . '%', $value, $subject);
                }

            }

        }

        // adiciona assinatura de e-mail se estiver definida
        if (!empty($emails_settings) && isset($emails_settings['email_signature'])) {
            $body .= "\n\r" . $emails_settings['email_signature'];
        }

        /**
         * @link https://developer.wordpress.org/reference/functions/wp_mail/
         */
        $send = false;
        $send = \wp_mail(sanitize_email($params['email']), $subject, \apply_filters('the_content', $body), $headers, $attachments);

        // caso o e-mail enviado seja HTML, retorna ao formato defaut (text/plain)
        \add_filter('wp_mail_content_type', [$this, 'text_content_type']);

        if (!$send) {

            global $wp_mail_errors;
            global $phpmailer;

            if (!isset($wp_mail_errors))
                $wp_mail_errors = [];

            if (isset($phpmailer)) {
                $wp_mail_errors['error'] = $phpmailer->ErrorInfo;
            }

            $this->error($wp_mail_errors['error']);

        }

    }

    protected function text_content_type() {
        return 'text/plain';
    }

}