<?php

namespace Iande;

use Controller;

class User extends Controller
{
    /**
     * Parsea o usuário para retorno na api
     *
     * @param \WP_User $user
     * 
     * @filter iande.parse_user
     * 
     * @return void
     */
    protected function parse_user(\WP_User $user)
    {
        $parsed_user = $user->data;

        unset($parsed_user->user_pass, $parsed_user->user_url);

        $parsed_user->roles = $user->roles;

        $parsed_user = \apply_filters('iande.parse_user', $parsed_user, $user);

        return $parsed_user;
    }

    /**
     * Renderiza a página de login
     *
     * @param array $params
     * @return void
     */
    function view_login(array $params = [])
    {
        $this->render('login');
    }

    /**
     * Verifica se o usuário está logado
     *
     * @return void
     */
    function endpoint_is_logged_in()
    {
        $logged_in = is_user_logged_in();

        $this->success($logged_in);
    }

    /**
     * Retorna o usuário logado
     *
     * @return void
     */
    function endpoint_get_logged_in()
    {
        $this->require_authentication();

        $user = $this->parse_user(\wp_get_current_user());

        $this->success($user);
    }

    /**
     * Tenta fazer o login do usuário
     *
     * @param array $params
     * 
     * @action iande.login_before
     * @action iande.login_success
     * @action iande.login_fail
     * 
     * @return void
     */
    function endpoint_login(array $params = [])
    {

        if (empty($params['email'])) {
            $this->error(__('Informe o endereço de email', 'iande'));
        }

        if (empty($params['password'])) {
            $this->error(__('Informe a senha', 'iande'));
        }

        do_action('iande.login_before', $params);

        $user = wp_signon(['user_login' => $params['email'], 'user_password' => $params['password']]);
        if ($user instanceof \WP_User) {
            do_action('iande.login_success', $user);

            $user = $this->parse_user($user);
            $this->success($user);
        } else {
            do_action('iande.login_fail', $params);

            $this->error(__('Credenciais inválidas', 'iande'), 401);
        }
    }

    /**
     * Desloga o usuário
     *
     * @return void
     */
    function endpoint_logout()
    {
        \wp_logout();
        $this->success(true);
    }

    /**
     * Cria um novo usuário.
     *
     * @param array $params
     * 
     * @action iande.before_create_user
     * @action iande.after_create_user
     * @action iande.login_success
     * 
     * @return void
     */
    function endpoint_create(array $params = [])
    {
        foreach (['first_name', 'last_name', 'email', 'phone', 'password'] as $field) {
            if (empty($params[$field])) {
                $this->error(__('Todos os campos são obrigatórios', 'iande'));
            }
        }

        if (strlen($params['password']) < 6) {
            $this->error(__('Já senha deve ter no mínimo seis caracteres', 'iande'));
        }

        if (\get_user_by('email', $params['email'])) {
            $this->error(__('Já existe um usuário com este endereço de email', 'iande'));
        }

        \do_action('iande.before_create_user', $params);

        $new_user_id = \wp_insert_user([
            'user_login' => $params['email'],
            'user_email' => $params['email'],
            'user_pass' => $params['password'],
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'],
            'display_name' => $params['first_name'] . ' ' . $params['last_name']
        ]);

        if ($new_user_id instanceof \WP_Error) {
            $this->error($new_user_id->get_error_messages());
        }

        \add_user_meta($new_user_id, 'phone', $params['phone']);

        \do_action('iande.after_create_user', $new_user_id);

        $user = \wp_signon(['user_login' => $params['email'], 'user_password' => $params['password']]);

        \do_action('iande.login_success', $user);

        $this->success($this->parse_user($user));
    }
}
