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
}
