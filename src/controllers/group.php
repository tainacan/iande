<?php

namespace IandePlugin;

use Controller;

class Group extends Controller
{

    /**
     * Cria um agendamento novo
     *
     * @param array $params
     * @return array
     */
    function endpoint_create(array $params = [])
    {

        $this->validate($params);

        $args = [
            'post_type'   => 'group',
            'post_author' => get_current_user_id(),
            'post_title'  => '',
            'post_status' => 'draft'
        ];

        $group_id = wp_insert_post($args);

        $this->set_group_metadata($group_id, $params);

        $group = $this->get_parsed_group($group_id);

        $this->success($group);

    }

    /**
     * Retorna um grupo pelo id
     *
     * @param array $params
     * @return array
     */
    function endpoint_get(array $params = [])
    {

        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $group = $this->get_parsed_group($params['ID']);

        if (!is_iande_staff() && $group->user_id != \get_current_user_id()) {
            $this->error(__('Essa ação requer privilégios administrativos', 'iande'));
        }

        if (empty($group)) {
            return; // 404
        }

        $this->success($group);

    }

    /**
     * Retorna todos os grupos agendados
     *
     * @param array $params
     * @return array
     */
    function endpoint_list (array $params = [])
    {
        $this->require_credentials();

        $args = [
            'post_type'      => 'group',
            'post_status'    => ['publish'],
            'posts_per_page' => -1,
        ];

        $groups = get_posts($args);

        if (empty($groups)) {
            return $this->success([]);
        }

        $parsed_groups = [];

        foreach ($groups as $key => $group) {
            $parsed_groups[] = $this->get_parsed_group($group->ID);
        }

        $parsed_groups = array_filter($parsed_groups);

        if (empty($parsed_groups)) {
            return $this->success([]);
        }

        $this->success($parsed_groups);
    }

    /**
     * Retorna todos os grupos assinalados pelo educador
     *
     * @param array $params
     * @return array
     */
    function endpoint_list_agenda (array $params = [])
    {
        $this->require_credentials();

        $args = [
            'post_type'      => 'group',
            'post_status'    => ['publish'],
            'posts_per_page' => -1,
            'meta_query'     => [[
                'key'   => 'educator_id',
                'value' => \get_current_user_id()
            ]]
        ];

        $groups = get_posts($args);

        if (empty($groups)) {
            return $this->success([]);
        }

        $parsed_groups = [];

        foreach ($groups as $key => $group) {
            $parsed_groups[] = $this->get_parsed_group($group->ID);
        }

        $parsed_groups = array_filter($parsed_groups);

        if (empty($parsed_groups)) {
            return $this->success([]);
        }

        $this->success($parsed_groups);
    }

    /**
     * Atualiza os metadados do grupo
     *
     * @param array $params
     * @return array
     *
     */
    function endpoint_update(array $params = []) {
        $this->require_credentials();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $metadata_definition = get_group_metadata_definition();

        /**
         * Verifica se os parâmetros ($params) são aceitos nesse endpoint
         */
        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $metadata_definition) && $key != 'ID') {
                $this->error(\sprintf(__("O parâmetro [%s] é inválido", 'iande'), \esc_html($key)));
            }
        }

        $this->validate($params);

        $this->set_group_metadata($params['ID'], $params);

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Atualiza os metadados de checkin do grupo
     *
     * @param array $params
     * @return array
     */
    function endpoint_checkin_update(array $params = []) {
        $this->require_credentials();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->is_educator($params['ID']);

        $metadata_definition = get_group_checkin_metadata_definition();

        /**
         * Verifica se os parâmetros ($params) são aceitos nesse endpoint
         */
        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $metadata_definition) && $key != 'ID') {
                $this->error(\sprintf(__("O parâmetro [%s] é inválido", 'iande'), \esc_html($key)));
            }
        }

        $this->validate($params);

        /**
         * Permite a criação/edição do checkin apenas após a data da visitação agendada
         */
        $date = \get_post_meta($params['ID'], 'date', true);

        if (date('Y-m-d') < $date) {
            $this->error(__("O check-in não pode ser realizado/alterado antes da data de visitação agendada", 'iande'));
        }

        $this->set_group_metadata($params['ID'], $params);

        $this->email_after_visiting($params['ID']);

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Atualiza os metadados de feedback do grupo
     *
     * @param array $params
     * @return array
     */
    function endpoint_feedback_update(array $params = []) {
        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $metadata_definition = get_group_feedback_metadata_definition();

        $this->is_owner_group($params['ID']);

        /**
         * Verifica se os parâmetros ($params) são aceitos nesse endpoint
         */
        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $metadata_definition) && $key != 'ID') {
                $this->error(\sprintf(__("O parâmetro [%s] é inválido", 'iande'), \esc_html($key)));
            }
        }

        $this->validate($params);

        /**
         * Permite a criação/edição da avaliação do visitante apenas após o checkin
         */
        $has_checkin = \get_post_meta($params['ID'], 'has_checkin', true);

        if (!$has_checkin) {
            $this->error(__('A avaliação do visitante não pode ser realizada antes do check-in', 'iande'));
        }

        /**
         * Permite a criação/edição da avaliação apenas para grupos que compareceram à visita
         */
        $checkin_showed = \get_post_meta($params['ID'], 'checkin_showed', true);
        if ($checkin_showed !== 'yes') {
            $this->error(__('A avaliação do visitante não pode ser realizada se grupo não compareceu à visita', 'iande'));
        }

        $this->set_group_metadata($params['ID'], $params);

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Atualiza os metadados de report do grupo
     *
     * @param array $params
     * @return array
     */
    function endpoint_report_update(array $params = []) {
        $this->require_credentials();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->is_educator($params['ID']);

        $metadata_definition = get_group_report_metadata_definition();

        /**
         * Verifica se os parâmetros ($params) são aceitos nesse endpoint
         */
        foreach ($params as $key => $value) {
            if (!array_key_exists($key, $metadata_definition) && $key != 'ID') {
                $this->error(\sprintf(__("O parâmetro [%s] é inválido", 'iande'), \esc_html($key)));
            }
        }

        $this->validate($params);

        /**
         * Permite a criação/edição da avaliação do educador apenas após o checkin
         */
        $has_checkin = \get_post_meta($params['ID'], 'has_checkin', true);

        if (!$has_checkin) {
            $this->error(__('A avaliação do educador não pode ser realizada antes do check-in', 'iande'));
        }

        /**
         * Permite a criação/edição da avaliação apenas para grupos que compareceram à visita
         */
        $checkin_showed = \get_post_meta($params['ID'], 'checkin_showed', true);

        if ($checkin_showed !== 'yes') {
            $this->error(__('A avaliação do educador não pode ser realizada se grupo não compareceu à visita', 'iande'));
        }

        $this->set_group_metadata($params['ID'], $params);

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Atribui um educador ao grupo
     *
     * @param array $params
     * @param array $params['ID'] ID do grupo
     * @param array $params['educator_id'] ID do usuário educador
     * @return array
     *
     */
    function endpoint_assign_educator(array $params = []) {
        $this->require_credentials();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (empty($params['educator_id'])) {
            $this->error(__('O parâmetro educator_id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || !is_numeric($params['educator_id']) || intval($params['ID']) != $params['ID'] || intval($params['educator_id']) != $params['educator_id']) {
            $this->error(__('O parâmetro deve ser um número inteiro', 'iande'));
        }

        $this->validate($params);

        /**
         * Permite a atribuição do educador apenas enquanto o check-in não for realizado
         */
        $has_checkin = \get_post_meta($params['ID'], 'has_checkin', true);

        if ($has_checkin) {
            $this->error(__('A atribuição do educador não pode ser realizada após o check-in', 'iande'));
        }

        $this->set_group_metadata($params['ID'], $params);

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Remove o educador do grupo
     *
     * @param array $params
     * @param array $params['ID'] ID do grupo
     * @return array
     */
    function endpoint_unassign_educator(array $params = []) {
        $this->require_credentials();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        /**
         * Permite a desatribuição do educador apenas enquanto o checkin não for realizado
         */
        $has_checkin = \get_post_meta($params['ID'], 'has_checkin', true);

        if ($has_checkin) {
            $this->error(__('A desatribuição do educador não pode ser realizada após o check-in', 'iande'));
        }

        \delete_post_meta($params['ID'], 'educator_id');

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Renderiza a tela de agenda do educador
     *
     * @param array $params
     * return void
     */
    function view_agenda(array $params = [])
    {
        $this->require_credentials();
        $this->render_vue(__('Minha agenda', 'iande'), 'agenda');
    }

    /**
     * Renderiza a tela de check-in
     *
     * @param array $params
     * return void
     */
    function view_checkin(array $params = [])
    {
        $this->require_credentials();
        $this->render_vue(__('Check-in', 'iande'), 'checkin');
    }

    /**
     * Renderiza a tela de avaliação do usuário
     *
     * @param array $params
     * return void
     */
    function view_feedback(array $params = [])
    {
        $this->require_authentication();
        $this->render_vue(__('Avaliação', 'iande'), 'feedback');
    }

    /**
     * Renderiza a listagem de grupos, para impressão
     *
     * @param array $params
     * return void
     */
    function view_print(array $params = []) {
        $this->require_credentials();
        $this->render_vue(__('Próximos grupos', 'iande'), 'print-groups');
    }

    /**
     * Renderiza o calendário de grupos para educadores
     *
     * @param array $params
     * @return void
     */
    function view_list(array $params = [])
    {
        $this->require_credentials();
        $this->render_vue(__('Calendário geral', 'iande'), 'list-groups');
    }

    /**
     * Renderiza a tela de avaliação do educador
     *
     * @param array $params
     * return void
     */
    function view_report(array $params = [])
    {
        $this->require_authentication();
        $this->render_vue(__('Avaliação', 'iande'), 'report');
    }

    /**
     * Insere ou atualiza os metadados do grupo
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_group_metadata(int $post_id, array $params = [])
    {
        $metadata_definition = get_all_group_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {
                \update_post_meta($post_id, $key, $params[$key]);
            }
        }
    }

    /**
     * Retorna um agendamento parseado
     *
     * @param integer $group_id
     * @return object
     */
    function get_parsed_group(int $group_id)
    {
        $group = \get_post($group_id);

        if (is_null($group)) {
            return null;
        }

        $meta = \get_post_meta($group_id);

        return $this->parse_group($group, $meta);
    }

    /**
     * Parsea o grupo para retorno na API
     *
     * @param \WP_Post $group
     * @param array $metadata
     *
     * @filter iande.parse_group
     *
     * @return object
     */
    function parse_group(\WP_Post $group, array $metadata = [])
    {
        $parsed_group = (object) [
            'ID'          => $group->ID,
            'user_id'     => $group->post_author,
            'title'       => $group->post_title,
            'post_status' => $group->post_status
        ];

        $metadata_definition = get_all_group_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $parsed_group->$key = isset($metadata[$key][0]) ? \maybe_unserialize($metadata[$key][0]) : null;
        }

        return $parsed_group;
    }

    /**
     * Valida os metadados do grupo
     *
     * @param array     $params Valores dos metadados
     * @param boolean   $validate_missing_requirements Se deve validar a obrigatoriedade dos campos não passados no array $params
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false)
    {
        $metadata_definition = get_all_group_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {

            // validação de campos obrigatórios
            if ($definition->required && empty($params[$key])) {
                if ($validate_missing_requirements) {
                    $this->error($definition->required);
                } elseif (isset($params[$key])) {
                    $this->error($definition->required);
                }
            }

            if (!empty($params[$key])) {
                $validation_fn = $definition->validation;
                $validation    = $validation_fn($params[$key], $params);
                $valid         = $validation === true;
                if (!$valid) {
                    $this->error($validation);
                }
            }

        }
    }

    /**
     * Verifica se o usuário está assinalado como educador do grupo
     *
     * @param int $group_id ID do grupo para verificação
     * @param int $user_id ID do usuário para verificação ou vazio para verificar o usuário logado
     * @param string $error_message Mensagem de erro
     */
    function is_educator($group_id, $user_id = '', $error_message = '') {

        $educator_id = \get_post_meta($group_id, 'educator_id', true);

        if (empty($user_id))
            $user_id = \get_current_user_id();

        if ($user_id != $educator_id) {
            if (\wp_is_json_request()) {
                $error_message = $error_message ?: __('Essa ação requer privilégios administrativos', 'iande');
                $this->error($error_message, 403);
            } else {
                $this->render('login', ['next' => $_SERVER['REQUEST_URI']]);
            }
        }

        return true;

    }

    /**
     * Verifica se o usuário é autor do grupo
     *
     * @param int $group_id ID do grupo para verificação
     * @param int $user_id ID do usuário para verificação ou vazio para verificar o usuário lodado
     * @param string $error_message Mensagem de erro
     */
    function is_owner_group($group_id, $user_id = '', $error_message = '') {

        $group = \get_post($group_id);

        if (empty($user_id))
            $user_id = \get_current_user_id();

        if ($user_id != $group->post_author) {
            if (\wp_is_json_request()) {
                $error_message = $error_message ?: __('Essa ação requer privilégios administrativos', 'iande');
                $this->error($error_message, 403);
            } else {
                $this->render('login', ['next' => $_SERVER['REQUEST_URI']]);
            }
        }

        return true;

    }

    /**
     * Envia e-mail pós-visita
     *
     * @param int $group_id ID do grupo para enviar o e-mail
     */
    function email_after_visiting( $group_id )
    {

        $group       = \get_post( $group_id );
        $appointment = \get_post( $group->appointment_id );

        if ( is_null( $group ) || is_null( $appointment ) )
            return null;

        if ( ! $group->confirmation_sent_after_visiting && $group->has_checkin && $group->checkin_showed === 'yes' ) {

            $email_params = [
                'email'          => $appointment->responsible_email,
                'cc'             => \get_the_author_meta( 'user_email', $appointment->post_author ),
                'interpolations' => [
                    'nome'      => $appointment->responsible_first_name,
                    'data'      => date( 'd/m/Y', strtotime( $group->date ) ),
                    'exposicao' => \get_the_title( $group->exhibition_id ),
                    'link'      => \home_url( '/iande/group/feedback/?ID=' . $group->ID )
                ]
            ];

            $this->email( 'email_after_visiting', $email_params );

            \update_post_meta( $group->ID, 'confirmation_sent_after_visiting', '1' );

        }

    }

}