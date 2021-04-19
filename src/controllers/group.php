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
            'post_status' => 'pending'
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

        $this->require_admin();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro id deve ser um número inteiro', 'iande'));
        }

        $group = $this->get_parsed_group($params['ID']);

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
        $this->require_authentication();

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
        $this->require_authentication();

        $args = [
            'post_type'      => 'group',
            'post_status'    => ['publish'],
            'posts_per_page' => -1,
            'meta_query'     => [[
                'meta_key'   => 'educator_id',
                'meta_value' => get_current_user_id()
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
     * @todo verificar permissões de edição dos metadados
     */
    function endpoint_update(array $params = [])
    {

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->validate($params);

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
    function endpoint_assign_educator(array $params = [])
    {
        $this->require_authentication();

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
    function endpoint_unassign_educator(array $params = [])
    {
        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        \delete_post_meta($params['ID'], 'educator_id');

        $group = $this->get_parsed_group($params['ID']);

        $this->success($group);

    }

    /**
     * Renderiza a tela de check-in
     *
     * @param array $params
     * return void
     */
    function view_checkin(array $params = [])
    {
        $this->require_admin();
        $this->render('checkin');
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
        $this->render('feedback');
    }

    /**
     * Renderiza o calendário de grupos para educadores
     *
     * @param array $params
     * @return void
     */
    function view_list(array $params = [])
    {
        $this->require_admin();
        $this->render('list-groups');
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
        $this->render('report');
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

        $metadata_definition = get_all_group_metadata_definition($params);

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

}