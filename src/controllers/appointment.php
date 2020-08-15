<?php

namespace Iande;

use Controller;

class Appointment extends Controller
{
    /**
     * Renderiza a página de criação de reserva
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = [])
    {
        $this->render('create-appointment');
    }

    /**
     * Cria um agendamento novo
     * 
     * @param array $params 
     * 
     * @action iande.before_create_appointment
     * @action iande.after_create_appointment
     * 
     * @return void 
     */
    function endpoint_create(array $params = [])
    {

        $this->require_authentication();
        $this->validate($params);

        \do_action('iande.before_create_appointment', $params);

        $args = [
            'post_type' => 'appointment',
            'post_author' => get_current_user_id(),
            'post_title' => '',
            'post_status' => 'draft'
        ];


        $appointment_id = wp_insert_post($args);

        $this->set_appointment_metadata($appointment_id, $params);

        $appointment = $this->get_parsed_appointment($appointment_id);

        \do_action('iande.after_create_appointment', $appointment_id, $appointment);

        $this->success($appointment);
    }

    /**
     * Atualiza o agendamento
     *
     * @param array $params
     * 
     * @action iande.before_update_appointment
     * @action iande.after_update_appointment
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

        $appointment = get_post($params['ID']);

        $this->check_user_permission($appointment);

        \do_action('iande.before_update_appointment', $params);

        $this->set_appointment_metadata($params['ID'], $params);

        \do_action('iande.after_update_appointment', $params);

        $parsed_appointment = $this->get_parsed_appointment($params['ID']);

        $this->success($parsed_appointment);
    }

    /**
     * Retorna um agendamento pelo id
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

        $appointment = $this->get_parsed_appointment($params['ID']);

        if (empty($appointment)) {
            return; // 404
        }

        $this->check_user_permission($appointment);

        $this->success($appointment);
    }

    /**
     * Retorna todos agendamentos do usuário
     *
     * @return void
     */
    function endpoint_list()
    {

        $this->require_authentication();

        $user_id = \get_current_user_id();

        $args = array(
            'author'         =>  $user_id,
            'post_type'      => 'appointment',
            'post_status'    => ['publish', /* 'draft' */],
            'posts_per_page' => 9999
        );

        $appointments = get_posts($args);

        if (empty($appointments)) {
            return $this->success([]);
        }

        $parsed_appointments = [];

        foreach ($appointments as $key => $appointment) {
            $parsed_appointments[] = $this->get_parsed_appointment($appointment->ID);
        }

        $parsed_appointments = array_filter($parsed_appointments);

        if (empty($parsed_appointments)) {
            return $this->success([]);
        }

        $this->success($parsed_appointments);
    }

    /**
     * Verifica se o usuário tem permissão para ver o agendamento
     * Se não tiver permissão retorna o erro na API
     *
     * @param WP_Post|object $appointment
     * 
     * @todo aplicar o current_user_can (https://developer.wordpress.org/reference/functions/current_user_can/)
     *       para que considere a validação do role do usuário, por exemplo adminstradores devem conseguir ver
     * 
     * @return void
     */
    function check_user_permission ($appointment){

        $user_id = $appointment instanceof \WP_Post ? $appointment->post_author : $appointment->user_id;

        if ($user_id != get_current_user_id()) {
            $this->error(__('Você não tem permissão para ver este agendamento', 'iande'), 403);
        }
    }

    /**
     * Valida os metadados do agendamento
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

        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            // validação de campo obrigatórios
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
     * Parsea o agendamento para retorno na API
     *
     * @param \WP_Post $appointment
     * @param array $metadata
     * 
     * @filter iande.parse_appointment 
     * 
     * @return object
     */
    function parse_appointment(\WP_Post $appointment, array $metadata = [])
    {
        $pased_appointment = (object) [
            'ID' => $appointment->ID,
            'user_id' => $appointment->post_author,
            'title' => $appointment->post_title,
        ];

        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $pased_appointment->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
        }

        $pased_appointment = \apply_filters('iande.parse_appointment', $pased_appointment, $appointment, $metadata);

        return $pased_appointment;
    }

    /**
     * Retorna um agendamento parseado
     *
     * @param integer $appointment_id
     * @return object
     */
    function get_parsed_appointment(int $appointment_id)
    {
        $appointment = get_post($appointment_id);

        if (is_null($appointment)) {
            return null;
        }

        $meta = get_post_meta($appointment_id);

        return $this->parse_appointment($appointment, $meta);
    }

    /**
     * Insere ou atualiza os metadados do agendamento
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_appointment_metadata(int $post_id, array $params = [])
    {
        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {
                \update_post_meta($post_id, $key, $params[$key]);
            }
        }
    }
}
