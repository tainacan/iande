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
        $this->render_vue(__('Novo agendamento', 'iande'), 'create-appointment');
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
        $this->render_vue(__('Editar agendamento', 'iande'), 'edit-appointment');
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
        $this->render_vue(__('Seus agendamentos', 'iande'), 'list-appointments');
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
        $this->validate_params($params, true, true);

        \do_action('iande.before_create_appointment', $params);

        $args = [
            'post_type'   => 'appointment',
            'post_author' => get_current_user_id(),
            'post_title'  => '',
            'post_status' => 'draft'
        ];

        $appointment_id = \wp_insert_post($args);

        $this->set_appointment_metadata($appointment_id, $params);

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
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->validate_params($params, true, true);

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
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $appointment = \get_post($params['ID']);

        if ($appointment === null) {

            $this->error(__('Agendamento não encontrado', 'iande'));

        } elseif (\get_current_user_id() == $appointment->post_author || is_iande_staff()) {

            \do_action('iande.before_cancel_appointment', $params);

            $cancel_reason = __('Cancelado pelo usuário', 'iande');
            if (!empty($params['reason'])) {
                $cancel_reason = $params['reason'];
            }
            \update_post_meta($params['ID'], 'reason_cancel', $cancel_reason, '');

            $update_appointment = [
                'ID'          => $params['ID'],
                'post_status' => 'canceled'
            ];
            \wp_update_post($update_appointment);

            $groups = \get_post_meta($params['ID'], 'groups', true);

            if (!empty($groups) && is_array($groups)) {
                foreach ($groups as $group) {
                    $update_group = [
                        'ID'          => $group,
                        'post_status' => 'canceled'
                    ];
                    \wp_update_post($update_group);
                }
            }

            // envia o e-mail de cancelamento para o responsavel do agendamento
            $email_params = [
                'email' => $appointment->responsible_email,
                'cc'    => $this->get_author_email($params['ID']),
                'interpolations' => [
                    'nome'       => $appointment->responsible_first_name,
                    'exposicao'  => \get_the_title($appointment->exhibition_id),
                    'grupos'     => $groups,
                    'link'       => \home_url('/iande/appointment/create/'),
                    'motivo'     => $cancel_reason,
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
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
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
            'post_status'    => ['publish', 'pending', 'canceled'],
            'posts_per_page' => -1,
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
     * @param array $params
     * @return void
     */
    function endpoint_list_published($params)
    {

        $this->require_credentials();

        $args = [
            'post_type'      => 'appointment',
            'post_status'    => ['publish'],
            'posts_per_page' => -1,
            'meta_query'     => []
        ];

        if (!empty($params['pending'] && $params['pending'] == '1')) {
            $args['post_status'][] = 'pending';
        }

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

            $groups = \get_post_meta($params['ID'], 'groups', true);

            if (!empty($groups) && is_array($groups)) {
                foreach ($groups as $group) {
                    $update_group = [
                        'ID'          => $group,
                        'post_status' => $params['post_status'],
                    ];
                    \wp_update_post($update_group);
                }
            }

            if ($current_post_status == 'draft' && $new_post_status == 'pending') {

                $requested_exemption = \get_post_meta($params['ID'], 'requested_exemption', true);

                if ($requested_exemption == 'yes' && use_exemption()) {

                    // envia o e-mail de solicitação de isenção para o responsável do agendamento
                    $email_params = [
                        'email' => \get_post_meta($params['ID'], 'responsible_email', true),
                        'cc'    => $this->get_author_email($params['ID']),
                        'interpolations' => [
                            'nome'        => \get_post_meta($params['ID'], 'responsible_first_name', true),
                            'exposicao'   => \get_the_title(\get_post_meta($params['ID'], 'exhibition_id', true)),
                            'grupos'      => $groups,
                            'email_museu' => \cmb2_get_option('iande_emails_settings', 'email_contact'),
                        ]
                    ];
                    $this->email('email_exemption', $email_params);

                }

            } elseif ($current_post_status == 'pending' && $new_post_status == 'publish') {

                if ($this->validate($params['ID']) && !$confirmation_sent) {

                    // envia o e-mail de confirmação para o responsável do agendamento
                    $email_params = [
                        'email' => \get_post_meta($params['ID'], 'responsible_email', true),
                        'cc'    => $this->get_author_email($params['ID']),
                        'interpolations' => [
                            'nome'      => \get_post_meta($params['ID'], 'responsible_first_name', true),
                            'exposicao' => \get_the_title(\get_post_meta($params['ID'], 'exhibition_id', true)),
                            'grupos'    => $groups,
                            'link'      => \home_url('/iande/appointment/list')
                        ]
                    ];
                    $this->email('email_confirmed', $email_params);

                    // adiciona o envio de lembrete do agendamento
                    foreach ($groups as $group) {

                        $email_params['interpolations']['grupos'] = [$group];

                        $event_date = \get_post_meta($group, 'date', true);
                        $interval   = strtotime($event_date) - strtotime('now');
                        $interval   = floor($interval / (60 * 60 * 24));

                        if ($interval > 14) {
                            \wp_schedule_single_event(strtotime('-7 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                        } elseif ($interval > 8) {
                            \wp_schedule_single_event(strtotime('-4 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                        } elseif ($interval > 4) {
                            \wp_schedule_single_event(strtotime('-2 days', strtotime($event_date)), 'send_email_reminder', [$email_params]);
                        }

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

        if ($user_id != get_current_user_id() && !is_iande_staff()) {
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
    function validate_params (array $params = [], $validate_missing_requirements = false, $force = false) {
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
     * Verifica se o agendamento está devidamente validado
     *
     * @param integer $appointment_id
     * @return boolean
     */
    function validate (int $appointment_id) {
        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {

            if (isset($definition->required) && !empty($definition->required)) {
                $metadata = \get_post_meta($appointment_id, $key, true);

                if (empty($metadata)) {
                    $this->error($definition->required);
                }
            }

        }

        return true;
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
     * Parseia o agendamento para retorno na API
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
        $parsed_appointment = (object) [
            'ID'          => $appointment->ID,
            'user_id'     => $appointment->post_author,
            'title'       => $appointment->post_title,
            'post_status' => $appointment->post_status
        ];

        $metadata_definition = get_appointment_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if ($key == 'num_people') {
                $parsed_appointment->$key = isset($metadata[$key][0]) ? $this->count_people_appointment($appointment->ID) : null;
            } elseif ($key == 'groups') {
                $parsed_appointment->$key = isset($metadata[$key][0]) ? $this->get_parsed_group(\maybe_unserialize($metadata[$key][0])) : null;
            } else {
                $parsed_appointment->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
            }
        }

        $parsed_appointment = \apply_filters('iande.parse_appointment', $parsed_appointment, $appointment, $metadata);

        return $parsed_appointment;
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
                'scholarity'      => $group->scholarity,
                'has_checkin'     => $group->has_checkin,
                'checkin_showed'  => $group->checkin_showed,
                'has_feedback'    => $group->has_feedback,
                'has_report'      => $group->has_report,
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

        $this->check_availability($exhibition_id, $groups);

        foreach($groups as $each_group) {

            $group = (array) $each_group;

            if(!isset($group['ID']) || empty($group['ID'])) {

                $meta_input = [];

                foreach ($group as $key => $value) {

                    if (array_key_exists($key, $metadata_definition) && !empty($value)) {
                        $meta_input[$key] = $value;
                    }

                    $meta_input['appointment_id'] = $appointment_id;
                    $meta_input['exhibition_id'] = $exhibition_id;

                }

                // Cria o título do grupo com informações do agendamento
                // {nome-grupo} - {data} {horário}"
                $title = $group['name'] . ' - ' . date_format(date_create($meta_input['date']), 'd/m/Y') . ' ' . $meta_input['hour'];

                $new_group = [
                    'post_type'   => 'group',
                    'post_author' => \get_current_user_id(),
                    'post_title'  => $title,
                    'post_status' => 'draft',
                    'meta_input'  => $meta_input
                ];

                $new_group_id = \wp_insert_post($new_group, true);

                if (!is_wp_error($new_group_id)) {
                    $group_to_appointment[] = $new_group_id;
                } else {
                    $this->error(__('O grupo não pode ser criado', 'iande'));
                }

            } else {

                foreach ($group as $key => $value) {
                    \update_post_meta($group['ID'], $key, $value);
                }

                $group_to_appointment[] = $group['ID'];

            }

        }

        if (!empty($group_to_appointment)) {
            \update_post_meta($appointment_id, 'groups', $group_to_appointment);
        }

    }

    /**
     * Define/atualiza o título do agendamento
     *
     * @param integer $appointment_id
     * @return void
     */
    function set_appointment_title(int $appointment_id) {

        $num_people    = \get_post_meta($appointment_id, 'num_people', true);
        $exhibition_id = \get_post_meta($appointment_id, 'exhibition_id', true);
        $group_size    = \get_post_meta($exhibition_id, 'group_size', true);

        $count_groups = ceil($num_people / $group_size);

        $name = \get_post_meta($appointment_id, 'name', true);

        $string_groups = ($count_groups > 1) ? 'grupos' : 'grupo';

        if ($name) {
            $title = $name . ' - ' . $count_groups . ' ' . $string_groups;
        } else {
            $title = $count_groups . ' ' . $string_groups;
        }

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

    /**
     * Retorna o e-mail do autor do post
     *
     * @param number $post_id ID do post
     * @return string
     */
    function get_author_email($post_id) {
        return \get_the_author_meta('user_email', \get_post($post_id)->post_author);
    }

    /**
     * Verifica se todos os grupos podem ser agendados simultaneamente
     *
     * @param integer $exhibition_id
     * @param array   $groups
     * @return void
     */
    protected function check_availability($exhibition_id, $groups) {

        if (!is_numeric($exhibition_id) || intval($exhibition_id) != $exhibition_id) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        if (\get_post_type($exhibition_id) != 'exhibition') {
            $this->error(__('O ID informado não é uma exposição válida', 'iande'));
        }

        // Pega o tamanho do slot (quantidade de grupos por horário)
        $group_slots = \get_post_meta($exhibition_id, 'group_slot', true);

        $agenda = [];

        $group_ids = [];
        foreach ($groups as $each_group) {
            $group = (array) $each_group;

            if ($group['ID']) {
                $group_ids[] = (int) $group['ID'];
            }
        }

        foreach ($groups as $each_group) {
            $group = (array) $each_group;

            if(!isset($group['date']) || empty($group['date']) || !isset($group['hour']) || empty($group['hour'])) {
                $this->error(__('Data e horário são obrigatórios para um grupo', 'iande'));
            }

            $date = $group['date'];
            $hour = $group['hour'];
            $time = $date . ' ' . $hour;

            if (!isset($agenda[$time])) {
                $args = [
                    'post_type'   => 'group',
                    'numberposts' => -1,
                    'post_status' => ['pending', 'publish'],
                    'exclude'     => $group_ids,
                    'fields'      => 'ids',
                    'meta_query'  => [
                        [
                            'key'   => 'exhibition_id',
                            'value' => $exhibition_id
                        ],
                        [
                            'key'     => 'date',
                            'value'   => $group['date'],
                            'compare' => '=',
                            'type'    => 'DATE'
                        ],
                        [
                            'key'     => 'hour',
                            'value'   => $group['hour'],
                            'compare' => '='
                        ]
                    ]
                ];

                $time_groups = \get_posts($args);
                $agenda[$time] = $time_groups;
            }

            if ($group['ID']) {
                $agenda[$time][] = (int) $group['ID'];
            } else {
                $agenda[$time][] = -1;
            }

           // Se a quantidade de grupos for maior que o tamanho do slot, retorna erro
            if (count($agenda[$time]) > $group_slots) {
                $this->error(\sprintf(__('Horário indisponível nessa exposição: %s %s', 'iande'), date_format(date_create($date), 'd/m/Y'), $hour));
            }
        }
    }

}
