<?php

namespace IandePlugin;

use Controller;

class Appointment extends Controller
{

    /**
     * Renderiza a página de criação de agendamento
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = [])
    {
        $this->require_authentication();
        $this->render('create-appointment');
    }

    /**
     * Renderiza a página de confirmação de agendamento
     *
     * @param array $params
     * @return void
     */
    function view_confirm(array $params = [])
    {
        $this->require_authentication();
        $this->render('confirm-appointment');
    }

    /**
     * Renderiza a página de edição de agendamento
     *
     * @param array $params
     * @return void
     */
    function view_edit(array $params = [])
    {
        $this->require_authentication();
        $this->render('edit-appointment');
    }

    /**
     * Renderiza a página de listagem de agendamentos do usuário
     *
     * @param array $params
     * @return void
     */
    function view_list(array $params = [])
    {
        $this->require_authentication();
        $this->render('list-appointments');
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
    function endpoint_create(array $params = []) {

        $this->require_authentication();
        $this->validate($params, true, true);

        \do_action('iande.before_create_appointment', $params);

        $args = [
            'post_type'   => 'appointment',
            'post_author' => get_current_user_id(),
            'post_title'  => '',
            'post_status' => 'draft'
        ];

        $appointment_id = \wp_insert_post($args);

        $this->set_appointment_metadata($appointment_id, $params);

        \update_post_meta($appointment_id, 'step', 1);

        $this->set_appointment_title($appointment_id);

        if (isset($params['groups']) && !empty($params['groups'])) {
            $this->set_appointment_groups($appointment_id, $params['groups']);
        }

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
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->validate($params, true, true);

        $appointment = get_post($params['ID']);

        $this->check_user_permission($appointment);

        \do_action('iande.before_update_appointment', $params);

        $this->set_appointment_metadata($params['ID'], $params);

        $this->set_appointment_title($params['ID']);

        if (isset($params['groups']) && !empty($params['groups'])) {
            $this->set_appointment_groups($params['ID'], $params['groups']);
        }

        \do_action('iande.after_update_appointment', $params);

        $parsed_appointment = $this->get_parsed_appointment($params['ID']);

        $this->success($parsed_appointment);
    }

    /**
     * Cancela um agendamento
     *
     * @param array $params
     *
     * @action iande.before_cancel_appointment
     * @action iande.after_cancel_appointment
     *
     * @return void
     */
    function endpoint_cancel(array $params = []) {

        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro id deve ser um número inteiro', 'iande'));
        }

        $appointment = \get_post($params['ID']);

        if ($appointment === null) {

            $this->error(__('Agendamento não encontrado', 'iande'));

        } elseif (\get_current_user_id() == $appointment->post_author || \current_user_can('manage_options')) {

            \do_action('iande.before_cancel_appointment', $params);

            if (!empty($params['reason'])) {
                \update_post_meta($params['ID'], 'reason_cancel', $params['reason'], '');
            } else {
                \update_post_meta($params['ID'], 'reason_cancel', __('Cancelado pelo usuário', 'iande'), '');
            }

            $update_appointment = array(
                'ID'          => $params['ID'],
                'post_status' => 'canceled'
            );
            \wp_update_post($update_appointment);

            // envia o e-mail de cancelamento para o responsavel do agendamento
            $email_params = [
                'email' => $appointment->responsible_email,
                'interpolations' => [
                    'nome'       => $appointment->responsible_first_name,
                    'exposicao'  => \get_the_title($appointment->exhibition_id),
                    'data'       => date('d/m/Y', strtotime($appointment->date)),
                    'horario'    => $appointment->hour,
                    'visitantes' => $this->count_people_appointment($params['ID'])
                ]
            ];
            $this->email('email_canceled', $email_params);

            \update_post_meta($params['ID'], 'confirmation_sent', '0');

            $appointment = $this->get_parsed_appointment($params['ID']);

            \do_action('iande.after_cancel_appointment', $appointment);

            $this->success($appointment);

        }

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
            'post_status'    => ['publish', 'pending', 'canceled', 'draft'],
            'posts_per_page' => 9999,
            'meta_query'     => [
                [
                    'key'     => 'step',
                    'value'   => 2,
                    'compare' => '>=',
                    'type'    => 'NUMERIC'
                ]
            ]
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
     * Retorna todos os agendamentos confirmados
     *
     * @return void
     */
    function endpoint_list_published($params)
    {

        $this->require_admin();

        $args = [
            'post_type'      => 'appointment',
            'post_status'    => ['publish'],
            'posts_per_page' => 9999,
            'meta_query'     => []
        ];

        if (!empty($params['exhibition'])) {
            $args['meta_query'][] = [
                'key'   => 'exhibition_id',
                'value' => $params['exhibition'],
            ];
        }

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
     * Muda o step do agendamento quando todos os campos estão válidos
     *
     * @return void
     */
    function endpoint_advance_step(array $params = []) {

        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        if (get_post_type($params['ID']) != 'appointment') {
            $this->error(__('O ID informado não é um agendamento válido', 'iande'));
        }
        $step = get_post_meta($params['ID'], 'step', true);

        if ($this->validate_step($params['ID']) && $step == 1) {

            update_post_meta($params['ID'], 'step', 2, $step);

            $requested_exemption = get_post_meta($params['ID'], 'requested_exemption', true);

            if ($requested_exemption == 'yes' && use_exemption()) {
                $email_template = 'email_pre_scheduling_exemption';
            } else {
                $email_template = 'email_pre_scheduling';
            }

            // envia o e-mail de pré-agendamento para o responsavel do agendamento
            $email_params = [
                'email' => \get_post_meta($params['ID'], 'responsible_email', true),
                'interpolations' => [
                    'nome'       => \get_post_meta($params['ID'], 'responsible_first_name', true),
                    'exposicao'  => \get_the_title($params['ID'], 'exhibition_id', true),
                    'data'       => date('d/m/Y', strtotime(get_post_meta($params['ID'], 'date', true))),
                    'horario'    => \get_post_meta($params['ID'], 'hour', true),
                    'visitantes' => $this->count_people_appointment($params['ID']),
                    'link'       => \home_url('/iande/appointment/confirm?ID=' . $params['ID'])
                ]
            ];
            $this->email($email_template, $email_params);

            $this->success(__('O agendamento passou para o próximo passo', 'iande'));

        } elseif($this->validate_step($params['ID']) && $step == 2) {

            update_post_meta($params['ID'], 'step', 3, $step);
            $this->success(__('O agendamento passou para o próximo passo e está aguardando confirmação', 'iande'));

        }

    }


    /**
     * Altera o status do agendamento
     *
     * @param integer   $params['ID']
     * @param string    $params['post_status']
     */
    function endpoint_set_status(array $params = []) {

        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        if (\get_post_type($params['ID']) != 'appointment') {
            $this->error(__('O ID informado não é um agendamento válido', 'iande'));
        }

        if (empty($params['post_status'])) {
            $this->error(__('O parâmetro status é obrigatório', 'iande'));
        }

        $default_post_status = ['publish', 'future', 'draft', 'pending', 'private', 'trash', 'auto-Draft', 'inherit', 'canceled'];
        if (!in_array(\sanitize_title($params['post_status']), $default_post_status)) {
            $this->error(__('O parâmetro status informado não é permitido', 'iande'));
        }

        $current_post_status = \get_post_status($params['ID']);
        $new_post_status     = $params['post_status'];

        $appointment = [
            'ID'          => $params['ID'],
            'post_status' => $params['post_status']
        ];
        $appointment_update = \wp_update_post($appointment, true);

        if (!is_wp_error($appointment_update) || !is_null($appointment_update)) {

            $confirmation_sent = \get_post_meta($params['ID'], 'confirmation_sent', true);

            if ($current_post_status == 'pending' && $new_post_status == 'publish') {

                if ($this->validate_step($params['ID']) && !$confirmation_sent) {

                    // envia o e-mail de confirmação para o responsavel do agendamento
                    $email_params = [
                        'email' => \get_post_meta($params['ID'], 'responsible_email', true),
                        'interpolations' => [
                            'nome'       => \get_post_meta($params['ID'], 'responsible_first_name', true),
                            'exposicao'  => \get_the_title(get_post_meta($params['ID'], 'exhibition_id', true)),
                            'data'       => date('d/m/Y', strtotime(get_post_meta($params['ID'], 'date', true))),
                            'horario'    => \get_post_meta($params['ID'], 'hour', true),
                            'visitantes' => $this->count_people_appointment($params['ID'])
                        ]
                    ];
                    $this->email('email_confirmed', $email_params);

                    // adiciona o envio de lembrete do agendamento
                    $event_date = \get_post_meta($params['ID'], 'date', true);

                    $interval   = strtotime($event_date) - strtotime('now');
                    $interval   = floor($interval / (60 * 60 * 24));

                    if ($interval > 14) {
                        \wp_schedule_single_event(strtotime('-7 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                    } elseif ($interval > 8) {
                        \wp_schedule_single_event(strtotime('-4 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                    } elseif ($interval > 4) {
                        \wp_schedule_single_event(strtotime('-2 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                    }

                    \update_post_meta($params['ID'], 'confirmation_sent', '1');
                }

            }

            $this->success($appointment);

        }

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
     * @param boolean $force Defina como true para conseguir validar campos não obrigatórios
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false, $force = false)
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

            // validação de campos obrigatórios
            if ($definition->required && empty($params[$key]) && !$force) {
                if ($validate_missing_requirements) {
                    $this->error($definition->required);
                } else if (isset($params[$key]) || empty($params[$key])) {
                    $this->error($definition->required);
                }
            }

            if (!empty($params[$key])) {
                $validation_fn = $definition->validation;
                $validation = $validation_fn($params[$key], $params);
                $valid = $validation === true;
                if (!$valid) {
                    $this->error($validation);
                }
            }

        }
    }

    /**
     * Verifica o step do agendamento de acordo com o metadata required_step
     *
     * @param integer $appointment_id
     * @return integer $step
     */
    function validate_step(int $appointment_id) {

        $step = get_post_meta($appointment_id, 'step', true);

        if ($step) {

            $metadata_definition = get_appointment_metadata_definition();

            foreach ($metadata_definition as $key => $definition) {

                if (isset($definition->required_step) && !empty($definition->required_step)) {

                    if ($definition->required_step <= $step) {

                        $metadata = get_post_meta($appointment_id, $key, true);

                        if (empty($metadata)) {
                            $this->error(__('Faltam alguns campos obrigatórios, revise e tente novamente'));
                        }

                    }

                }

            }

            return true;

        }

    }

    /**
     * Verifica se todos os campos obrigatórios do agendamento estão preenchidos
     *
     * @param integer $appointment_id
     * @return void
     */
    function check_metadata_appointment($appointment_id) {

        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {

            if ($definition->required) {
                $metadata = get_post_meta($appointment_id, $key, true);
                if (empty($metadata)) {
                    $this->error($definition->required);
                }
            }
        }

        return true;

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
            'ID'          => $appointment->ID,
            'user_id'     => $appointment->post_author,
            'title'       => $appointment->post_title,
            'post_status' => $appointment->post_status
        ];

        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if ($key == 'num_people') {
                $pased_appointment->$key = isset($metadata[$key][0]) ? $this->count_people_appointment($appointment->ID) : null;
            } elseif ($key == 'groups') {
                $pased_appointment->$key = isset($metadata[$key][0]) ? $this->get_parsed_group(\maybe_unserialize($metadata[$key][0])) : null;
            } else {
                $pased_appointment->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
            }
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
     * Retorna um ou mais grupos parseados
     *
     * @param array $groups
     * @return void
     */
    function get_parsed_group($groups) {

        $parsed_groups = [];
        foreach ($groups as $group_id) {

            $group = \get_post(intval($group_id));

            $parsed_groups[] = [
                'age_range'       => $group->age_range,
                'ID'              => $group->ID,
                'date'            => $group->date,
                'disabilities'    => (isset($group->disabilities) && !empty($group->disabilities)) ? $group->disabilities : [],
                'exhibition_id'   => $group->exhibition_id,
                'hour'            => $group->hour,
                'languages'       => (isset($group->languages) && !empty($group->languages)) ? $group->languages : [],
                'name'            => $group->name,
                'num_people'      => $group->num_people,
                'num_responsible' => $group->num_responsible,
                'scholarity'      => $group->scholarity
            ];

        }

        return $parsed_groups;

    }

    /**
     * Insere ou atualiza os metadados do agendamento
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_appointment_metadata(int $post_id, array $params = []) {
        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {

                \update_post_meta($post_id, $key, $params[$key]);

            }
        }
    }

    /**
     * Cria os grupos do agendamento
     *
     * @param int   $appointment_id
     * @param array $groups
     * @return void
     *
     */
    function set_appointment_groups($appointment_id, $groups = []) {
        
        $metadata_definition = get_group_metadata_definition();

        $group_to_appointment = [];

        $exhibition_id = \get_post_meta($appointment_id, 'exhibition_id', true);

        foreach($groups as $each_group) {

            $group = (array) $each_group;

            if(!isset($group['ID']) || empty($group['ID'])) {

                $meta_input = [];

                foreach ($group as $key => $value) {

                    if (array_key_exists($key, $metadata_definition) && !empty($value)) {
                        $meta_input[$key] = $value;
                    }

                    $meta_input['exhibition_id'] = $exhibition_id;

                }

                if(!isset($meta_input['date']) || empty($meta_input['date']) || !isset($meta_input['hour']) || empty($meta_input['hour'])) {
                    $this->error(__('Data e horário são obrigatórios para criar um grupo', 'iande'));
                }

                $this->check_availability($exhibition_id, $meta_input['date'], $meta_input['hour']);

                // Cria o título do grupo com informações do agendamento
                // {nome-grupo} - {data} {horário}"
                $title = $group['name'] . ' - ' . date_format(date_create($meta_input['date']), 'd/m/Y') . ' ' . $meta_input['hour'];

                $new_group = [
                    'post_type'   => 'group',
                    'post_author' => \get_current_user_id(),
                    'post_title'  => $title,
                    'post_status' => 'pending',
                    'meta_input'  => $meta_input
                ];

                $new_group_id = \wp_insert_post($new_group, true);

                if (!is_wp_error($new_group_id)) {
                    $group_to_appointment[] = $new_group_id;
                } else {
                    $this->error(__('O grupo não pode ser criado', 'iande'));
                }

            } else {

                if (!isset($group['date']) || empty($group['date']) || !isset($group['hour']) || empty($group['hour'])) {
                    $this->error(__('Data e horário são obrigatórios para editar um grupo', 'iande'));
                }

                $this->check_availability($exhibition_id, $group['date'], $group['hour']);

                foreach ($group as $key => $value) {
                    \update_post_meta($group['ID'], $key, $value);
                }

                $group_to_appointment[] = $group['ID'];

            }

        }

        \update_post_meta($appointment_id, 'groups', $group_to_appointment);

    }

    /**
     * Define/atualiza o título do agendamento
     *
     * @param integer $appointment_id
     * @return void
     */
    function set_appointment_title(int $appointment_id) {

        $responsible_first_name = \get_post_meta($appointment_id, 'responsible_first_name', true);
        $responsible_last_name  = \get_post_meta($appointment_id, 'responsible_last_name', true);
        $date                   = \get_post_meta($appointment_id, 'date', true);
        $hour                   = \get_post_meta($appointment_id, 'hour', true);

        // "{nome-responsavel} {sobrenome-responsavel} - {data} {horário}"
        $title = $responsible_first_name . ' ' . $responsible_last_name . ' - ' . $date . ' ' . $hour;

        $slug  = \sanitize_title($title);
        $slug  = \wp_unique_post_slug($slug, $appointment_id, get_post_status($appointment_id), 'appointment', 0);

        if ($title && $slug) {
            $post = array(
                'ID'         => $appointment_id,
                'post_title' => \apply_filters('title', $title),
                'post_name'  => $slug
            );
            \wp_update_post($post);
        }
    }

    /**
     * Retorna a quantidade de pessoas no agendamento informado
     *
     * @param string    $appointment_id
     * @param boolean   $sum
     * @return void
     */
    function count_people_appointment(string $appointment_id, $sum = false)
    {

        $num_people_groups = \maybe_unserialize(get_post_meta($appointment_id, 'groups', true));
        $num_people = get_post_meta($appointment_id, 'num_people', true);


        if ($sum && is_array($num_people_groups) && !empty($num_people_groups)) {

            foreach ($num_people_groups as $group) {
                $count[] = \get_post_meta($group, 'num_people', true);
            }

            return array_sum($count);

        } elseif(!empty($num_people)) {

            return $num_people;

        }

        return false;

    }

}

