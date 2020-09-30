<?php

add_action( 'manage_posts_extra_tablenav', 'iande_export_button', 20, 1);
function iande_export_button($which) {
    global $typenow;

    if ('appointment' === $typenow && 'top' === $which && \current_user_can( 'manage_options' )) {
        ?>
        <input type="submit" name="export_all_appointments" id="export_all_appointments" class="button button-primary" value="Exportar Agendamentos" />
        <input type="submit" name="export_all_groups" id="export_all_groups" class="button button-primary" value="Exportar Grupos" />
        <?php
	}

}

add_action('init', 'iande_export_appointment');
function iande_export_appointment() {

    // Check for current user privileges
    if (!\current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['export_all_appointments']) && \is_admin()) {

        $format_binary = function ($value) {
            if (empty($value) || $value == 'no') {
                return 'Não';
            } elseif ($value == 'yes') {
                return 'Sim';
            }
        };

        $format_group_nature = function ($value) {
            if ($value == 'institutional') {
                return 'Institucional';
            } elseif ($value == 'other') {
                return 'Outra';
            }
        };

        $appointments = \get_posts([
            'post_type'      => 'appointment',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ]);

        if ($appointments) {

            ob_start();

            // Cria o nome do CSV
            $current_datetime = \sanitize_title(date('Ymd-His', current_time('timestamp', 0)));
            $blogname = \sanitize_title(get_bloginfo('name'));
            $filename = $blogname . '-export-appointments-' . $current_datetime . '.csv';

            // Cabeçalhos do CSV
            $header_row = [
                'ID',
                'Título',
                'Nome da visita',
                'Exibição',
                'Objetivo da visita',
                'Objetivo da visita (Outro)',
                'Nome do responsável',
                'Sobrenome do responsável',
                'E-mail do responsável',
                'Telefone do responsável',
                'Relação com a instituição',
                'Relação com a instituição (Outro)',
                'Natureza do grupo',
                'Instituição',
                'Requisitou isenção?',
                'Já visitou o museu?',
                'Preparou a visita?',
                'Como preparou a visita',
                'Comentários adicionais',
                'Grupo(s)'
            ];

            $data_rows = [];

            foreach ($appointments as $appointment) {

                $id = $appointment->ID;

                $groups = \get_post_meta($id, 'groups', true);
                if (is_string($groups)) {
                    $groups = json_decode($groups);
                }

                $group_ids = join(', ', $groups);

                // Cada linha do array deve corresponder aos cabeçalhos do CSV
                $row = [
                    $id,
                    $appointment->post_title,
                    \get_post_meta($id, 'name', true),
                    \get_the_title(\get_post_meta($id, 'exhibition_id', true)),
                    \get_post_meta($id, 'purpose', true),
                    \get_post_meta($id, 'purpose_other', true),
                    \get_post_meta($id, 'responsible_first_name', true),
                    \get_post_meta($id, 'responsible_last_name', true),
                    \get_post_meta($id, 'responsible_email', true),
                    \get_post_meta($id, 'responsible_phone', true),
                    \get_post_meta($id, 'responsible_role', true),
                    \get_post_meta($id, 'responsible_role_other', true),
                    $format_group_nature(\get_post_meta($id, 'group_nature', true)),
                    \get_the_title(\get_post_meta($id, 'institution_id', true)),
                    $format_binary(\get_post_meta($id, 'requested_exemption', true)),
                    $format_binary(\get_post_meta($id, 'has_visited_previously', true)),
                    $format_binary(\get_post_meta($id, 'has_prepared_visit', true)),
                    \get_post_meta($id, 'how_prepared_visit', true),
                    \get_post_meta($id, 'additional_comment', true),
                    $group_ids
                ];
                $data_rows[] = $row;

            }

            $fh = @fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-type: text/csv');
            header("Content-Disposition: attachment; filename={$filename}");
            header('Expires: 0');
            header('Pragma: public');

            fputcsv($fh, $header_row);
            foreach ($data_rows as $data_row) {
                fputcsv($fh, $data_row);
            }
            fclose($fh);

            ob_end_flush();

            die();

        }
    }

}

add_action('init', 'iande_export_visit_groups');
function iande_export_visit_groups() {

    // Check for current user privileges
    if (!\current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['export_all_groups']) && \is_admin()) {

        $display_languages = function ($languages) {
            $arr = [];
            foreach ($languages as $language) {
                if (is_string($language)) {
                    $arr[] = $language;
                } elseif (!empty($language->other)) {
                    $arr[] = $language->other;
                } else {
                    $arr[] = $language->name;
                }
            }
            return join(', ', $arr);
        };

        $display_disabilities = function ($disabilities) {
            $arr = [];
            foreach ($disabilities as $disability) {
                if (!empty($disability->other)) {
                    $arr[] = $disability->other . ' (' . $disability->count . ')';
                } else {
                    $arr[] = $disability->type . ' (' . $disability->count . ')';
                }
            }
            return join(', ', $arr);
        };

        $appointments = \get_posts([
            'post_type'      => 'appointment',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ]);

        if ($appointments) {

            ob_start();

            // Cria o nome do CSV
            $current_datetime = \sanitize_title(date('Ymd-His', current_time('timestamp', 0)));
            $blogname = \sanitize_title(get_bloginfo('name'));
            $filename = $blogname . '-export-groups-' . $current_datetime . '.csv';

            // Cabeçalhos do CSV
            $header_row = [
                'Agendamento',
                'Data',
                'Hora',
                'Nome do grupo',
                'Quantidade prevista de pessoas',
                'Escolaridade',
                'Quantidade prevista de responsáveis',
                'Idiomas',
                'Pessoas com deficiência',
                'Nome do responsável',
                'Sobrenome do responsável',
                'E-mail do responsável',
                'Telefone do responsável',
                'Natureza do grupo',
                'Já visitou o museu?',
                'Preparou a visita?',
                'Como preparou a visita',
                'Comentários adicionais',
            ];

            $data_rows = [];

            foreach ($appointments as $appointment) {

                $id = $appointment->ID;

                $groups = \get_post_meta($id, 'group_list', true);
                if (is_string($groups)) {
                    $groups = json_decode($groups);
                }
                $groups = $groups->groups;

                foreach ($groups as $group) {

                    // Cada linha do array deve corresponder aos cabeçalhos do CSV
                    $row = [
                        $id,
                        \get_post_meta($id, 'date', true),
                        \get_post_meta($id, 'hour', true),
                        $group->name,
                        $group->num_people,
                        $group->scholarity,
                        $group->num_responsible,
                        $display_languages($group->languages),
                        $display_disabilities($group->disabilities),
                        \get_post_meta($id, 'responsible_first_name', true),
                        \get_post_meta($id, 'responsible_last_name', true),
                        \get_post_meta($id, 'responsible_email', true),
                        \get_post_meta($id, 'responsible_phone', true),
                        \get_post_meta($id, 'group_nature', true),
                        \get_post_meta($id, 'has_visited_previously', true),
                        \get_post_meta($id, 'has_prepared_visit', true),
                        \get_post_meta($id, 'how_prepared_visit', true),
                        \get_post_meta($id, 'additional_comment', true),
                    ];
                    $data_rows[] = $row;
                }
            }

            $fh = @fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-type: text/csv');
            header("Content-Disposition: attachment; filename={$filename}");
            header('Expires: 0');
            header('Pragma: public');

            fputcsv($fh, $header_row);
            foreach ($data_rows as $data_row) {
                fputcsv($fh, $data_row);
            }
            fclose($fh);

            ob_end_flush();

            die();

        }
    }

}