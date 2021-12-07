<?php

namespace IandePlugin;

if (!\wp_next_scheduled('iande.cron_daily')) {
    \wp_schedule_event(time(), 'daily', 'iande.cron_daily', []);
}

\add_action('iande.cron_daily', 'IandePlugin\auto_checkin');
\add_action('iande.cron_daily', 'IandePlugin\auto_expiry_abandoned_appointments');

/**
 * Faz check-in automaticamente se museu não o fez 10 dias depois da visita.
 *
 * Essa função assume um caminho feliz, isso é, a visita ocorreu e os dados do
 * agendamento batem.
 */
function auto_checkin () {
    $groups = \get_posts([
        'numberposts' => -1,
        'post_status' => ['publish'],
        'post_type'   => 'group',
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key' => 'date',
                'value'   => \date('Y-m-d', \strtotime('-10 days', time())),
                'compare' => '<=',
                'type'    => 'DATE',
            ],
            [
                'key'     => 'has_checkin',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ]);

    foreach ($groups as $group) {
        $metaInput = [
            '_auto_checkin' => '1',
            'has_checkin' => 'on',
            'checkin_showed' => 'yes',
            'checkin_hour' => 'yes',
            'checkin_num_people' => 'yes',
            'checkin_num_responsible' => 'yes',
            'checkin_disabilities' => 'yes',
            'checkin_languages' => 'yes',
            'checkin_scholarity' => 'yes',
            'checkin_age_range' => 'yes',
        ];

        $appointmentId = \get_post_meta($group->ID, 'appointment_id', true);

        $institutional = \get_post_meta($appointmentId, 'group_nature', true) === 'institutional';
        $metaInput['checkin_institutional'] = $institutional ? 'yes' : 'no';
        if ($institutional) {
            $metaInput['checkin_institution'] = \get_post_meta($appointmentId, 'institution_id', true);
        }

        \wp_update_post([
            'ID'         => $group->ID,
            'meta_input' => $metaInput,
        ]);
    }
}

/**
 * Expira automaticamente rascunhos abandonados há mais de 10 dias.
 */
function auto_expiry_abandoned_appointments () {
    $appointments = \get_posts([
        'numberposts' => -1,
        'post_status' => ['draft'],
        'post_type' => 'appointment',
        'date_query' => [
            [
                'column' => 'post_date',
                'before' => '10 days ago',
            ],
        ],
    ]);

    foreach ($appointments as $appointment) {
        \wp_update_post([
            'ID'          => $appointment->ID,
            'post_status' => 'trash',
            'meta_input'  => [
                '_auto_expiry' => '1',
            ],
        ]);

        $groups = \get_post_meta($appointment->ID, 'groups', true) ?: [];

        foreach ($groups as $group) {
            \wp_update_post([
                'ID'          => $group,
                'post_status' => 'trash',
                'meta_input'  => [
                    '_auto_expiry' => '1',
                ],
            ]);
        }
    }
}
