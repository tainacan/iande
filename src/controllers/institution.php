<?php

namespace Iande;

use Controller;

class Institution extends Controller
{
    /**
     * Renderiza a página de criação da instituição
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = [])
    {
        $this->render('create-institution');
    }

    /**
     * Cria uma nova instituição
     *
     * @param array $params
     *
     * @action iande.before_create_institution
     * @action iande.after_create_institution
     *
     * @return void
     */
    function endpoint_create(array $params = [])
    {

        $this->require_authentication();
        $this->validate($params);

        \do_action('iande.before_create_institution', $params);

        $args = [
            'post_type' => 'institution',
            'post_author' => get_current_user_id(),
            'post_title' => '',
            'post_status' => 'publish'
        ];


        $institution_id = wp_insert_post($args);

        $this->set_institution_metadata($institution_id, $params);

        $institution = $this->get_parsed_institution($institution_id);

        \do_action('iande.after_create_institution', $institution_id, $institution);

        $this->success($institution);
    }

    /**
     * Atualiza a instituição
     *
     * @param array $params
     *
     * @action iande.before_update_institution
     * @action iande.after_update_institution
     *
     * @return void
     */
    function endpoint_update(array $params = []) {

        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro id deve ser um número inteiro', 'iande'));
        }

        $this->validate($params);

        $institution = get_post($params['ID']);

        $this->check_user_permission($institution);

        \do_action('iande.before_update_institution', $params);

        $this->set_institution_metadata($params['ID'], $params);

        \do_action('iande.after_update_institution', $params);

        $parsed_institution = $this->get_parsed_institution($params['ID']);

        $this->success($parsed_institution);
    }

    /**
     * Retorna uma instituição pelo id
     *
     * @param array $params
     *
     * @return void
     */
    function endpoint_get(array $params = [])
    {
        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro id deve ser um número inteiro', 'iande'));
        }

        $institution = $this->get_parsed_institution($params['ID']);

        if (empty($institution)) {
            return; // 404
        }

        $this->check_user_permission($institution);

        $this->success($institution);
    }

    /**
     * Retorna todas instituições do usuário
     *
     * @return void
     */
    function endpoint_list()
    {

        $this->require_authentication();

        $user_id = \get_current_user_id();

        $args = array(
            'author'         =>  $user_id,
            'post_type'      => 'institution',
            'post_status'    => ['publish', /* 'draft' */],
            'posts_per_page' => 9999
        );

        $institutions = get_posts($args);

        if (empty($institutions)) {
            return $this->success([]);
        }

        $parsed_institutions = [];

        foreach ($institutions as $key => $institution) {
            $parsed_institutions[] = $this->get_parsed_institution($institution->ID);
        }

        $parsed_institutions = array_filter($parsed_institutions);

        if (empty($parsed_institutions)) {
            return $this->success([]);
        }

        $this->success($parsed_institutions);
    }



    /**
     * Verifica se o usuário tem permissão para ver a instituição
     * Se não tiver permissão retorna o erro na API
     *
     * @param WP_Post|object $institution
     *
     * @todo aplicar o current_user_can (https://developer.wordpress.org/reference/functions/current_user_can/)
     *       para que considere a validação do role do usuário, por exemplo adminstradores devem conseguir ver
     *
     * @return void
     */
    function check_user_permission ($institution){

        $user_id = $institution instanceof \WP_Post ? $institution->post_author : $institution->user_id;

        if ($user_id != get_current_user_id()) {
            $this->error(__('Você não tem permissão para ver esta instituição', 'iande'), 403);
        }
    }

    /**
     * Valida os metadados da instituição
     *
     * @param array $params Valores dos metadados
     * @param boolean $validate_missing_requirements Se deve validar a obrigatoriedade dos campos não passados no array $params
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false)
    {
        /* $params
        'purpose' => (object) [
            'type' => 'string',
            'required' => __("O objetivo é obrigatório", 'iande'),
            'validation' => function ($value) use ($purpose_options) {
                if (in_array($value, $purpose_options)) {
                    return true;
                } else {
                    return __('Objetivo inválido', 'iande');
                }
            }
        ],
         */

        $metadata_definition = get_institution_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            // validação de campos obrigatórios
            if ($definition->required && empty($params[$key])) {
                if ($validate_missing_requirements) {
                    $this->error($definition->required);
                } else if (isset($params[$key])) {
                    $this->error($definition->required);
                }
            }

            if (!empty($params[$key])) {
                $validation_fn = $definition->validation;
                $validation = $validation_fn($params[$key]);
                $valid = $validation === true;
                if (!$valid) {
                    $this->error($validation);
                }
            }
        }
    }

    /**
     * Parsea a instituição para retorno na API
     *
     * @param \WP_Post $institution
     * @param array $metadata
     *
     * @filter iande.institution
     *
     * @return object
     */
    function parse_institution(\WP_Post $institution, array $metadata = [])
    {
        $pased_institution = (object) [
            'ID' => $institution->ID,
            'user_id' => $institution->post_author,
            'title' => $institution->post_title,
        ];

        $metadata_definition = get_institution_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $pased_institution->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
        }

        $pased_institution = \apply_filters('iande.parse_institution', $pased_institution, $institution, $metadata);

        return $pased_institution;
    }

    /**
     * Retorna uma instituição parseado
     *
     * @param integer $institution_id
     * @return object
     */
    function get_parsed_institution(int $institution_id)
    {
        $institution = get_post($institution_id);

        if (is_null($institution)) {
            return null;
        }

        $meta = get_post_meta($institution_id);

        return $this->parse_institution($institution, $meta);
    }

    /**
     * Insere ou atualiza os metadados da instituição
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_institution_metadata(int $post_id, array $params = [])
    {
        $metadata_definition = get_institution_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {
                \update_post_meta($post_id, $key, $params[$key]);
            }
        }
    }
}
