<?php

namespace IandePlugin;

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
     * @return object
     */
    protected function parse_user(\WP_User $user)
    {
        $parsed_user = $user->data;

        unset($parsed_user->user_pass, $parsed_user->user_url);

        $parsed_user->roles = $user->roles;

        if ($user->__isset('first_name') && !empty($user->__get('first_name')))
            $parsed_user->first_name = $user->__get('first_name');

        if ($user->__isset('last_name') && !empty($user->__get('last_name')))
        $parsed_user->last_name = $user->__get('last_name');

        if ($user->__isset('phone') && !empty($user->__get('phone')))
            $parsed_user->phone = $user->__get('phone');

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
        $this->render('login', ['next' => '/iande/user/welcome']);
    }

    /**
     * Renderiza a página de criação de usuários
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = [])
    {
        $this->render_vue(__('Cadastro de usuário', 'iande'), 'create-user');
    }

    /**
     * Renderiza a página de edição do usuário atual
     *
     * @param array $params
     * @return void
     */
    function view_edit(array $params = [])
    {
        $this->require_authentication();
        $this->render_vue(__('Editar usuário', 'iande'), 'edit-user');
    }

    /**
     * Renderiza a página de alteração de senha
     *
     * @param array $params
     * @return void
     */
    function view_change_password(array $params = [])
    {
        $this->require_authentication();
        $this->render_vue(__('Alterar senha', 'iande'), 'change-password');
    }

    /**
     * Renderiza a página de boas-vindas
     *
     * @param array $params
     * @return void
    */
    function view_welcome(array $params = [])
    {
        $this->require_authentication();
        $this->render_vue(__('Boas vindas', 'iande'), 'welcome');
    }

    /** Lista os usuários
     *
     * @param array $params
     * @return array
     */
    function endpoint_list(array $params = []) {
        $this->require_credentials();

        $query_args = [];

        if (!empty($params['cap'])) {
            $cap = $params['cap'];
            $role__in = [];

            foreach (\wp_roles()->roles as $role_slug => $role) {
                if (!empty($role['capabilities'][$cap])) {
                    $role__in[] = $role_slug;
                }
            }

            if (!empty($role__in)) {
                $query_args['role__in'] = $role__in;
            }
        }

        $users = \get_users($query_args);

        $parsed_users = \array_map([$this, 'parse_user'], $users);
        $this->success($parsed_users);
    }

    /**
     * Verifica se o usuário está logado
     *
     * @return void
     */
    function endpoint_is_logged_in()
    {
        $logged_in = \is_user_logged_in();

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

        if (!empty(compute_recaptcha_keys())) {

            if (empty($params['recaptcha'])) {
                $this->error(__('Preencha o Captcha', 'iande'));
            }

            if (!verify_recaptcha($params['recaptcha'])) {
                $this->error(__('Captcha inválido', 'iande'));
            }

        }

        \do_action('iande.login_before', $params);

        $user = wp_signon(['user_login' => $params['email'], 'user_password' => $params['password']]);
        if ($user instanceof \WP_User) {
            \do_action('iande.login_success', $user);

            $user = $this->parse_user($user);
            $this->success($user);
        } else {
            \do_action('iande.login_fail', $params);

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
     * Cria um novo usuário
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
            $this->error(__('A senha deve ter no mínimo seis caracteres', 'iande'));
        }

        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error(__('O endereço de email informado não é um endereço de emaill válido', 'iande'));
        }

        if (!empty(compute_recaptcha_keys())) {

            if (empty($params['recaptcha'])) {
                $this->error(__('Preencha o Captcha', 'iande'));
            }

            if (!verify_recaptcha($params['recaptcha'])) {
                $this->error(__('Captcha inválido', 'iande'));
            }

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
            'display_name' => $params['first_name'] . ' ' . $params['last_name'],
            'role' => 'iande_visitor',
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

    /**
     * Edita o usuário atual
     *
     * @param array $params
     *
     * @action iande.before_edit_user
     * @action iande.after_edit_user
     *
     * @return void
     */
    function endpoint_edit(array $params = []) {
        $this->require_authentication();

        $user = \wp_get_current_user();

        foreach (['first_name', 'last_name', 'email', 'phone'] as $field) {
            if (empty($params[$field])) {
                $this->error(__('Todos os campos são obrigatórios', 'iande'));
            }
        }

        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error(__('O endereço de email informado não é um endereço de emaill válido', 'iande'));
        }

        \do_action('iande.before_create_user', $params);

        $updated_user_id = \wp_update_user([
            'ID' => $user->ID,
            'user_login' => $params['email'],
            'user_email' => $params['email'],
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'],
            'display_name' => $params['first_name'] . ' ' . $params['last_name']
        ]);

        if ($updated_user_id instanceof \WP_Error) {
            $this->error($updated_user_id->get_error_messages());
        }

        \update_user_meta($updated_user_id, 'phone', $params['phone']);

        \do_action('iande.after_create_user', $updated_user_id);

        $this->success($this->parse_user(\wp_get_current_user()));
    }

    /**
     * Altera a senha do usuário atual
     *
     * @param array $params
     *
     * @action iande.before_edit_user
     * @action iande.after_edit_user
     *
     * @return void
     */
    function endpoint_change_password(array $params = []) {
        $this->require_authentication();

        $user_id = \get_current_user_id();

        if (!empty($params['password']) && strlen($params['password']) < 6) {
            $this->error(__('A senha deve ter no mínimo seis caracteres', 'iande'));
        }

        \do_action('iande.before_change_password', $params);

        if (!empty($params['password'])) {
            \wp_set_password($params['password'], $user_id);
        }

        \do_action('iande.after_change_password', $user_id);

        $this->success($this->parse_user(\wp_get_current_user()));
    }
}
